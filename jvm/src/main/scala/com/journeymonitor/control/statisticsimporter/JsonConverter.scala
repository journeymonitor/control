package com.journeymonitor.control.statisticsimporter

import java.io.{InputStream, OutputStream, StringReader}
import java.util.Date

import com.fasterxml.jackson.core.{JsonFactory, JsonToken}

import scala.collection.mutable
import scala.concurrent.Future
import scala.concurrent.ExecutionContext.Implicits.global

case class StatisticsModel(testresultId: String,
                           testresultDatetimeRun: String,
                           runtimeMilliseconds: Int,
                           numberOf200: Int,
                           numberOf400: Int,
                           numberOf500: Int)

trait JsonConverter {

  def inputStreamToStatistics(is: InputStream)(callback: (StatisticsModel) => _): Unit = {
    val jsonFactory = new JsonFactory()
    val jsonParser = jsonFactory.createParser(is)

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

        Future {
          callback(StatisticsModel(
            testresultId = values("testresultId"),
            testresultDatetimeRun = values("testresultDatetimeRun"),
            runtimeMilliseconds = values("runtimeMilliseconds").toInt,
            numberOf200 = values("numberOf200").toInt,
            numberOf400 = values("numberOf400").toInt,
            numberOf500 = values("numberOf500").toInt
          ))
        }
      }
    }

  }
}
