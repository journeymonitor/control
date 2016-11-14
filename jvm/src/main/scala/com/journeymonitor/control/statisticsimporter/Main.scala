package com.journeymonitor.control.statisticsimporter

import com.typesafe.scalalogging.Logger
import org.apache.http.client.methods.HttpGet
import org.apache.http.impl.client.HttpClients

import scala.concurrent.duration.Duration
import scala.concurrent.{Await, Future}
import scala.concurrent.ExecutionContext.Implicits.global
import scala.util.{Failure, Success}
import scala.util.control.NonFatal

object Main {
  def main(args: Array[String]): Unit = {

    val logger = Logger[this.type]

    val groupSize = 4

    val allUris = List(
      "http://80.69.45.171:8081/testcases/6E86B147-3F55-4DFB-9695-DFDC3E3F5747/statistics/latest/",
      "http://80.69.45.171:8081/testcases/6258D6F5-FE7C-44D5-8B41-324FDAE97CAF/statistics/latest/",
      "http://80.69.45.171:8081/testcases/5215E622-88DF-4FC6-A5E2-D26D8DDB5516/statistics/latest/",
      "http://80.69.45.171:8081/testcases/40B659CF-28BA-4D27-9F33-D109B735B019/statistics/latest/",
      "http://80.69.45.171:8081/testcases/F6ADDF52-1925-4735-9443-1BBEC3169130/statistics/latest/",
      "http://80.69.45.171:8081/testcases/6E86B147-3F55-4DFB-9695-DFDC3E3F5747/statistics/latest/",
      "http://80.69.45.171:8081/testcases/6258D6F5-FE7C-44D5-8B41-324FDAE97CAF/statistics/latest/",
      "http://80.69.45.171:8081/testcases/5215E622-88DF-4FC6-A5E2-D26D8DDB5516/statistics/latest/",
      "http://80.69.45.171:8081/testcases/40B659CF-28BA-4D27-9F33-D109B735B019/statistics/latest/",
      "http://80.69.45.171:8081/testcases/F6ADDF52-1925-4735-9443-1BBEC3169130/statistics/latest/",
      "http://80.69.45.171:8081/testcases/6E86B147-3F55-4DFB-9695-DFDC3E3F5747/statistics/latest/",
      "http://80.69.45.171:8081/testcases/6258D6F5-FE7C-44D5-8B41-324FDAE97CAF/statistics/latest/",
      "http://80.69.45.171:8081/testcases/5215E622-88DF-4FC6-A5E2-D26D8DDB5516/statistics/latest/",
      "http://80.69.45.171:8081/testcases/40B659CF-28BA-4D27-9F33-D109B735B019/statistics/latest/",
      "http://80.69.45.171:8081/testcases/F6ADDF52-1925-4735-9443-1BBEC3169130/statistics/latest/",
      "http://80.69.45.171:8081/testcases/6E86B147-3F55-4DFB-9695-DFDC3E3F5747/statistics/latest/",
      "http://80.69.45.171:8081/testcases/6258D6F5-FE7C-44D5-8B41-324FDAE97CAF/statistics/latest/",
      "http://80.69.45.171:8081/testcases/5215E622-88DF-4FC6-A5E2-D26D8DDB5516/statistics/latest/",
      "http://80.69.45.171:8081/testcases/40B659CF-28BA-4D27-9F33-D109B735B019/statistics/latest/",
      "http://80.69.45.171:8081/testcases/F6ADDF52-1925-4735-9443-1BBEC3169130/statistics/latest/",
      "http://80.69.45.171:8081/testcases/0F85A5DC-ADD3-48AE-8967-2EBF18826F8D/statistics/latest/"
    )

    val results = allUris.grouped(groupSize).toList.flatMap(uriGroup => {
      logger.info(s"Initiating work on: ${uriGroup.mkString(", ")}")
      val futures = for (uri <- uriGroup) yield {
        Future {
          try {
            val si = new StatisticsImporter(uri)
            val httpClient = HttpClients.createDefault()
            val httpGet = new HttpGet(uri)
            httpClient.execute(httpGet, si.responseHandler)
            s"Finished import of URI $uri"
          } catch {
            case NonFatal(t) => throw new Exception(s"Exception while working on URI $uri", t)
          }
        }
      }
      val lifted = futures.map(_.map(Success(_)).recover { case NonFatal(e) => Failure(e)})
      Await.result(Future.sequence(lifted), Duration.Inf)
    })

    val errorCount = results.foldLeft(0)((acc, cur) => cur match { case Failure(_) => acc + 1; case _ => acc + 0})
    if (errorCount == results.length) {
      logger.error("All done, but errors occured for all testcases.")
      results.foreach {
        case Failure(t) => logger.warn(t.getCause.toString)
        case _ => ()
      }
      System.exit(1)
    } else if (errorCount > 0) {
      logger.warn("All done, but errors occured for some testcases.")
      results.foreach {
        case Failure(t) => logger.warn(t.getCause.toString)
        case _ => ()
      }
      System.exit(1)
    } else {
      logger.info("All done, no errors occured.")
    }

  }
}
