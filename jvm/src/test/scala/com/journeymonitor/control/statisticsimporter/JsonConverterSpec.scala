package com.journeymonitor.control.statisticsimporter

import java.io.ByteArrayInputStream
import java.nio.charset.StandardCharsets
import java.text.SimpleDateFormat

import org.scalatest.{AsyncFunSpec, Matchers}

import scala.collection.mutable.{ArrayBuffer, ListBuffer}
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
      val statisticModels = ListBuffer[StatisticsModel]()
      val unitFuture = new Wrapper().inputStreamToStatistics(jsonInputStream) { statisticsModel =>
        statisticModels += statisticsModel
      }
      unitFuture.map { _ =>
        // Due to the asynchronous nature of the tested method, we cannot know the order of elements, which is why we map
        statisticModels.map { sm =>
          val format = new SimpleDateFormat("yyyy-MM-dd HH:mm:ssZ")
          sm.testresultId match {
            case "2E7A12F2-9F4B-49C2-8121-E4A185181FFD" =>
              sm.numberOf500 should be(0)
              sm.numberOf400 should be(0)
              sm.numberOf200 should be(200)
              sm.runtimeMilliseconds should be(13820)
              sm.testresultDatetimeRun should be(format.parse("2016-07-06 09:10:09+0000"))
            case "CEF49AED-2572-4B9E-88D8-E2113E4F3882" =>
              sm.numberOf500 should be(0)
              sm.numberOf400 should be(0)
              sm.numberOf200 should be(199)
              sm.runtimeMilliseconds should be(16441)
              sm.testresultDatetimeRun should be(format.parse("2016-07-06 09:05:06+0000"))
          }
        }
        statisticModels.length should be(2)
      }
    }
  }

}
