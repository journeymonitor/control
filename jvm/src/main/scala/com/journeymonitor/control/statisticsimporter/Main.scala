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

    val uris = List(
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/6E86B147-3F55-4DFB-9695-DFDC3E3F5747/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/6258D6F5-FE7C-44D5-8B41-324FDAE97CAF/statistics/latest/" /*,
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/5215E622-88DF-4FC6-A5E2-D26D8DDB5516/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/40B659CF-28BA-4D27-9F33-D109B735B019/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/F6ADDF52-1925-4735-9443-1BBEC3169130/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/6E86B147-3F55-4DFB-9695-DFDC3E3F5747/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/6258D6F5-FE7C-44D5-8B41-324FDAE97CAF/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/5215E622-88DF-4FC6-A5E2-D26D8DDB5516/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/40B659CF-28BA-4D27-9F33-D109B735B019/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/F6ADDF52-1925-4735-9443-1BBEC3169130/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/6E86B147-3F55-4DFB-9695-DFDC3E3F5747/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/6258D6F5-FE7C-44D5-8B41-324FDAE97CAF/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/5215E622-88DF-4FC6-A5E2-D26D8DDB5516/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/40B659CF-28BA-4D27-9F33-D109B735B019/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/F6ADDF52-1925-4735-9443-1BBEC3169130/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/6E86B147-3F55-4DFB-9695-DFDC3E3F5747/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/6258D6F5-FE7C-44D5-8B41-324FDAE97CAF/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/5215E622-88DF-4FC6-A5E2-D26D8DDB5516/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/40B659CF-28BA-4D27-9F33-D109B735B019/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/F6ADDF52-1925-4735-9443-1BBEC3169130/statistics/latest/",
      "http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/0F85A5DC-ADD3-48AE-8967-2EBF18826F8D/statistics/latest/" */
    )

    val futures = uris.map { uri =>
      Future {
        try {
          val si = new StatisticsImporter()
          val httpClient = HttpClients.createDefault()
          val httpGet = new HttpGet(uri)
          httpClient.execute(httpGet, si.responseHandler)
          s"Finished import of URI $uri"
        } catch {
          case NonFatal(t) => throw new Exception(s"Exception while working on URI $uri", t)
        }
      }
    }

    for (f <- futures) {
      f.onComplete {
        case Success(s) => logger.info("Success: " + s)
        case Failure(t) => logger.error(s"Error: ${t.getMessage} (${t.getCause.getMessage})")
      }
    }

    // End main thread after all futures have run.
    // Lift them to avoid unchecked exceptions on the main thread.
    val liftedFutures = futures.map(_.map(_ => Success()).recover { case NonFatal(t) => Failure(t) })
    val sequencedLiftedFutures = Future.sequence(liftedFutures)
    val sequencedLiftedResults = Await.result(sequencedLiftedFutures, Duration.Inf)

    val errorCount = sequencedLiftedResults.foldLeft(0)((acc, cur) => cur match { case Failure(_) => acc + 1; case _ => acc + 0})
    if (errorCount == sequencedLiftedResults.length) {
      logger.error("All done, but errors occured for all testcases.")
      System.exit(1)
    } else if (errorCount > 0) {
      logger.warn("All done, but errors occured for some testcases.")
      System.exit(1)
    } else {
      logger.info("All done, no errors occured.")
    }

  }
}
