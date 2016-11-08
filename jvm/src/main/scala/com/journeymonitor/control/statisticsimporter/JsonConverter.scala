package com.journeymonitor.control.statisticsimporter

import java.io.{InputStream, OutputStream, StringReader}
import java.text.SimpleDateFormat
import java.util.Date

import com.fasterxml.jackson.core.{JsonFactory, JsonToken}

import scala.collection.mutable
import scala.collection.mutable.{ArrayBuffer, ListBuffer}
import scala.concurrent.{ExecutionContext, Future}

case class StatisticsModel(testresultId: String,
                           testresultDatetimeRun: java.util.Date,
                           runtimeMilliseconds: Int,
                           numberOf200: Int,
                           numberOf400: Int,
                           numberOf500: Int)

trait JsonConverter {

  /**
    * @throws Exception Throws an exception if any one operation (within JSON parsing as well as callback operation) fails
    */
  def inputStreamToStatistics(inputStream: InputStream)(callback: (StatisticsModel) => Unit)(implicit ec: ExecutionContext): Future[Unit] = {

    try {

      val jsonFactory = new JsonFactory()
      val jsonParser = jsonFactory.createParser(inputStream)

      val fs = ListBuffer[Future[Unit]]()

      while (jsonParser.nextToken() != JsonToken.END_ARRAY) {
        if (jsonParser.getCurrentToken == JsonToken.START_OBJECT) {

          val values = mutable.Map[String, String]()

          while (jsonParser.nextToken() != JsonToken.END_OBJECT) {
            if (jsonParser.getCurrentToken == JsonToken.FIELD_NAME) {
              val fieldname = jsonParser.getText
              fieldname match {
                case "testresultId" | "testresultDatetimeRun" | "runtimeMilliseconds" | "numberOf200" | "numberOf400" | "numberOf500" =>
                  jsonParser.nextToken()
                  values += ((fieldname, jsonParser.getText))
                case _ =>
                  throw new Exception("Expected statistics JSON object field name, but got something else: " + jsonParser.getText)
              }
            }
          }

          fs += Future {
            val dateFormat = new SimpleDateFormat("yyyy-MM-dd HH:mm:ssZ") // must be created for each call because its not synchronized
            callback(StatisticsModel(
              testresultId = values("testresultId"),
              testresultDatetimeRun = dateFormat.parse(values("testresultDatetimeRun")),
              runtimeMilliseconds = values("runtimeMilliseconds").toInt,
              numberOf200 = values("numberOf200").toInt,
              numberOf400 = values("numberOf400").toInt,
              numberOf500 = values("numberOf500").toInt
            ))
          }
        }
      }

      Future.sequence(fs).map(_ => ()) // only return once all Futures are finished

    } catch {
      case e: Throwable => Future.failed(e)
    }
  }
}
