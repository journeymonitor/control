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
      val si = new StatisticsImporter()
      si.responseHandler.handleResponse(res)
    }
  }

}
