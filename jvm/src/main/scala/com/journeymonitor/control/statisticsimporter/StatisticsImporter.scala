package com.journeymonitor.control.statisticsimporter

import java.io.{ByteArrayInputStream, InputStream, OutputStream}
import java.nio.charset.StandardCharsets

import org.apache.http.{Header, HttpEntity, HttpResponse, ProtocolVersion}
import org.apache.http.client.ResponseHandler
import org.apache.http.client.methods.HttpGet
import org.apache.http.entity.BasicHttpEntity
import org.apache.http.impl.client.HttpClients
import org.apache.http.message.{BasicHttpResponse, BasicStatusLine}

import scala.concurrent.Await
import scala.concurrent.duration._
import scala.concurrent.ExecutionContext.Implicits.global

class StatisticsImporter extends JsonConverter {

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

    val jsonInputStream = new ByteArrayInputStream(json.getBytes(StandardCharsets.UTF_8))

    val res = new BasicHttpResponse(new BasicStatusLine(new ProtocolVersion("HTTP", 1, 1), 200, "OK"))
    val ent = new BasicHttpEntity()
    ent.setContent(jsonInputStream)
    res.setEntity(ent)
    rh.handleResponse(res)
  }

  val rh = new ResponseHandler[Unit]() {
    override def handleResponse(response: HttpResponse): Unit = {
      val entity = response.getEntity
      val inputStream: InputStream = entity.getContent()
      val done = inputStreamToStatistics(inputStream) { statisticsModel: StatisticsModel =>
        println(statisticsModel)
      } recover {
        case e => println(e)
      }
      Await.result(done, 10.seconds)
      println("done")
    }
  }

  //val httpClient = HttpClients.createDefault()
  //val httpGet = new HttpGet("http://localhost:4711/")

  //httpClient.execute(httpGet, rh)

}
