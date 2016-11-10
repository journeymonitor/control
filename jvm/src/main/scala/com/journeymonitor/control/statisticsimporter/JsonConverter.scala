package com.journeymonitor.control.statisticsimporter

import java.io.InputStream

import com.fasterxml.jackson.core.{JsonFactory, JsonToken}

import scala.collection.mutable
import scala.util.{Failure, Success, Try}

case class StatisticsModel(testresultId: String,
                           runtimeMilliseconds: Int,
                           numberOf200: Int,
                           numberOf400: Int,
                           numberOf500: Int)

trait JsonConverter {

  /**
    * @throws Exception Throws an exception if any one operation (within JSON parsing as well as callback operation) fails
    */
  def inputStreamToStatistics(inputStream: InputStream)(callback: (StatisticsModel) => Try[String]): List[Try[String]] = {

    try {

      val jsonFactory = new JsonFactory()
      val jsonParser = jsonFactory.createParser(inputStream)

      val s = Stream.continually(jsonParser.nextToken()).takeWhile(_ != JsonToken.END_ARRAY)

      val results: Stream[Try[String]] = for (t <- s) yield {
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

          val res = callback(StatisticsModel(
            testresultId = values("testresultId"),
            runtimeMilliseconds = values("runtimeMilliseconds").toInt,
            numberOf200 = values("numberOf200").toInt,
            numberOf400 = values("numberOf400").toInt,
            numberOf500 = values("numberOf500").toInt
          ))
          println(res)
          res
        } else {
          Success("")
        }
      }

      results.filter { result =>
        result.isSuccess && result.get != ""
      }.toList

    } catch {
      case t: Throwable =>
        println(t)
        List(Failure(t))
    }
  }
}
