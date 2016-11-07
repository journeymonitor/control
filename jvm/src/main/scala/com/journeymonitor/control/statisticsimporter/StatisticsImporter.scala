package com.journeymonitor.control.statisticsimporter

import java.io.InputStream

import com.fasterxml.jackson.core.{JsonFactory, JsonToken}
import org.apache.http.HttpResponse
import org.apache.http.client.ResponseHandler
import org.apache.http.client.methods.HttpGet
import org.apache.http.impl.client.HttpClients

/*

 */

class StatisticsImporter {

  def doImport(): Unit = {

    val json = """[
                 |  {
                 |    "numberOf500": 0,
                 |    "numberOf400": 0,
                 |    "numberOf200": 200,
                 |    "runtimeMilliseconds": 13820,
                 |    "testresultDatetimeRun": "2016-07-06 09:10:09+0000",
                 |    "testresultId": "2E7A12F2-9F4B-49C2-8121-E4A185181FFD"
                 |  },
                 |  {
                 |    "numberOf500": 0,
                 |    "numberOf400": 0,
                 |    "numberOf200": 199,
                 |    "runtimeMilliseconds": 16441,
                 |    "testresultDatetimeRun": "2016-07-06 09:05:06+0000",
                 |    "testresultId": "CEF49AED-2572-4B9E-88D8-E2113E4F3882"
                 |  }
                 |]
                 |""".stripMargin


    val jsonFactory = new JsonFactory()
    val jsonParser = jsonFactory.createParser(json)

    while (jsonParser.nextToken() != JsonToken.END_ARRAY) {
      if (jsonParser.getCurrentToken == JsonToken.START_OBJECT) {
        while (jsonParser.nextToken() != JsonToken.END_OBJECT) {
          if (jsonParser.getCurrentToken == JsonToken.FIELD_NAME) {
            jsonParser.getText match {
              case fieldName: String =>
                jsonParser.nextToken()
                println(fieldName + ": " + jsonParser.getText)
              case _ =>
                println("nix")
            }
          }
        }
      }
    }

    val rh = new ResponseHandler[Unit]() {
      override def handleResponse(response: HttpResponse): Unit = {
        val entity = response.getEntity
        val inputStream: InputStream = entity.getContent()
        val buffer = new Array[Byte](1024)

      }
    }

    //val httpClient = HttpClients.createDefault()
    //val httpGet = new HttpGet("http://localhost:4711/")

    //httpClient.execute(httpGet, rh)
  }

}
