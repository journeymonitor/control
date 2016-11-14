package com.journeymonitor.control.statisticsimporter

import java.io._
import java.text.SimpleDateFormat
import java.util.Date

import com.typesafe.scalalogging.Logger
import org.apache.http.HttpResponse
import org.apache.http.client.methods.HttpGet
import org.apache.http.client.{HttpClient, ResponseHandler}
import org.sqlite.{SQLiteErrorCode, SQLiteException}
import slick.jdbc.SQLiteProfile.api._
import slick.lifted.Tag

import scala.concurrent.Await
import scala.concurrent.ExecutionContext.Implicits.global
import scala.concurrent.duration._
import scala.util.Try
import scala.util.control.NonFatal

class StatisticsImporter extends JsonConverter {

  val logger = Logger[this.type]

  class StatisticsTable(tag: Tag) extends Table[(String, Int, Int, Int, Int, String, String)](tag, "statistics") {
    def testresultId = column[String]("testresult_id", O.PrimaryKey)
    def runtimeMilliseconds = column[Int]("runtimeMilliseconds")
    def numberOf200 = column[Int]("numberOf200")
    def numberOf400 = column[Int]("numberOf400")
    def numberOf500 = column[Int]("numberOf500")
    def createdAt = column[String]("created_at")
    def updatedAt = column[String]("updated_at")
    def * = (testresultId, runtimeMilliseconds, numberOf200, numberOf400, numberOf500, createdAt, updatedAt)
  }
  val statisticsTable = TableQuery[StatisticsTable]

  def doImport(httpClient: HttpClient): Unit = {
    val httpGet = new HttpGet("http://localhost:4711/")
    httpClient.execute(httpGet, responseHandler)
  }

  val sdf = new SimpleDateFormat("Y-M-d H:m:s")

  val responseHandler = new ResponseHandler[Unit]() {
    override def handleResponse(response: HttpResponse): Unit = {
      val entity = response.getEntity
      val inputStream: InputStream = entity.getContent()
      logger.info("Opening db connection")
      val db = Database.forURL("jdbc:sqlite:/var/tmp/journeymonitor-control-prod.sqlite3", driver = "org.sqlite.JDBC")
      try {
        inputStreamToStatistics(inputStream) { statisticsModel: StatisticsModel =>
          val insertAction = statisticsTable.insertOrUpdate(
            statisticsModel.testresultId,
            statisticsModel.runtimeMilliseconds,
            statisticsModel.numberOf200,
            statisticsModel.numberOf400,
            statisticsModel.numberOf500,
            sdf.format(new Date()),
            sdf.format(new Date())
          )

          Try {
            var i = 0
            var result = ""
            while (i < 10) {
              try {
                logger.debug(s"Going to persist $statisticsModel")
                val runFuture = db.run(insertAction) recover {
                  case NonFatal(t) =>
                    throw t
                }
                // Right now it looks like sqlite doesn't like any kind of parallelism whatsoever
                result = Await.result(runFuture.map(_ => "Finished " + statisticsModel.testresultId), Duration.Inf)
                logger.debug("done")
                if (i > 0) {
                  logger.warn(s"Managed to write after ${i + 1} retries.")
                }
                i = 10
              } catch {
                case e: org.sqlite.SQLiteException if e.getResultCode == SQLiteErrorCode.SQLITE_BUSY =>
                  logger.warn("SQLite db file is busy, trying again.")
                  i = i + 1
                  Thread.sleep(10)
              }
            }
            result
          }
        }

      } finally {
        logger.info("Closing db connection")
        db.close()
      }
    }
  }

}
