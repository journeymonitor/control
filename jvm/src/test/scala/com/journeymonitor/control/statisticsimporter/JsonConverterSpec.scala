package com.journeymonitor.control.statisticsimporter

import java.io.ByteArrayInputStream
import java.nio.charset.StandardCharsets

import org.scalatest.{AsyncFunSpec, Matchers}

import scala.collection.mutable.ArrayBuffer
import scala.concurrent.ExecutionContext.Implicits.global

class JsonConverterSpec extends AsyncFunSpec with Matchers {

  val json =
    """[
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
      | """.stripMargin

  val jsonInputStream = new ByteArrayInputStream(json.getBytes(StandardCharsets.UTF_8))

  class Wrapper extends JsonConverter

  describe("The JsonConverter trait") {
    it("should allow to convert an InputStream to statistics case objects") {
      val statisticModels = ArrayBuffer[StatisticsModel]()
      val futureUnit = new Wrapper().inputStreamToStatistics(jsonInputStream) { statisticsModel: StatisticsModel =>
          statisticModels += statisticsModel
        }
      futureUnit.map { _ =>
        statisticModels.length should be(2)
        (statisticModels(0).testresultId == "2E7A12F2-9F4B-49C2-8121-E4A185181FFD"
          || statisticModels(0).testresultId == "CEF49AED-2572-4B9E-88D8-E2113E4F3882") should be(true)
      }
    }
  }

}
