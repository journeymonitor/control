package com.journeymonitor.control.statisticsimporter

import java.io.ByteArrayInputStream
import java.nio.charset.StandardCharsets

import org.apache.http.ProtocolVersion
import org.apache.http.entity.BasicHttpEntity
import org.apache.http.message.{BasicHttpResponse, BasicStatusLine}
import org.scalatest.{FunSpec, Matchers}

class StatisticsImporterSpec extends FunSpec with Matchers {

  describe("The Statistics Importer") {
    it("should retrieve and persist statistics") {

      val jsonSeq = for (i <- 1 until 10000) yield {
        s""" {
          |    "numberOf500": 0,
          |    "numberOf400": 0,
          |    "numberOf200": 200,
          |    "runtimeMilliseconds": 13820,
          |    "testresultDatetimeRun": "2016-07-06 09:10:09+0000",
          |    "testresultId": "id$i"
          |  }
          |""".stripMargin
      }

      val json = jsonSeq.mkString(",")

      val jsonInputStream = new ByteArrayInputStream(("[" + json + "]").getBytes(StandardCharsets.UTF_8))

      val res = new BasicHttpResponse(new BasicStatusLine(new ProtocolVersion("HTTP", 1, 1), 200, "OK"))
      val ent = new BasicHttpEntity()
      ent.setContent(jsonInputStream)
      res.setEntity(ent)
      val si = new StatisticsImporter()
      si.responseHandler.handleResponse(res)
    }
  }

}
