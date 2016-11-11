package com.journeymonitor.control.statisticsimporter

import java.io._
import java.sql.{Date => SqlDate}

import org.apache.http.HttpResponse
import org.apache.http.client.methods.HttpGet
import org.apache.http.client.{HttpClient, ResponseHandler}
import slick.driver.SQLiteDriver.api._
import slick.lifted.Tag

import scala.concurrent.Await
import scala.concurrent.ExecutionContext.Implicits.global
import scala.concurrent.duration._
import scala.util.{Failure, Success, Try}

class StatisticsImporter extends JsonConverter {

  class StatisticsTable(tag: Tag) extends Table[(String, Int, Int, Int, Int, SqlDate, SqlDate)](tag, "statistics") {
    def testresultId = column[String]("testresult_id", O.PrimaryKey)
    def runtimeMilliseconds = column[Int]("runtimeMilliseconds")
    def numberOf200 = column[Int]("numberOf200")
    def numberOf400 = column[Int]("numberOf400")
    def numberOf500 = column[Int]("numberOf500")
    def createdAt = column[SqlDate]("created_at")
    def updatedAt = column[SqlDate]("updated_at")
    def * = (testresultId, runtimeMilliseconds, numberOf200, numberOf400, numberOf500, createdAt, updatedAt)
  }
  val statisticsTable = TableQuery[StatisticsTable]

  def doImport(httpClient: HttpClient): Unit = {
    val httpGet = new HttpGet("http://localhost:4711/")
    httpClient.execute(httpGet, responseHandler)
  }

  val db = Database.forURL("jdbc:sqlite:/var/tmp/journeymonitor-control-test.sqlite3", driver = "org.sqlite.JDBC")

  val responseHandler = new ResponseHandler[Unit]() {
    override def handleResponse(response: HttpResponse): Unit = {
      val entity = response.getEntity
      val inputStream: InputStream = entity.getContent()
      try {
        inputStreamToStatistics(inputStream) { statisticsModel: StatisticsModel =>
          val insertAction = statisticsTable.insertOrUpdate(
            statisticsModel.testresultId,
            statisticsModel.runtimeMilliseconds,
            statisticsModel.numberOf200,
            statisticsModel.numberOf400,
            statisticsModel.numberOf500,
            new SqlDate(System.currentTimeMillis),
            new SqlDate(System.currentTimeMillis)
          )

          Try {
            println("Going to persist " + statisticsModel.testresultId)
            val runFuture = db.run(insertAction)
            // Right now it looks like sqlite doesn't like any kind of parallelism whatsoever
            Await.result(runFuture.map(_ => "Finished " + statisticsModel.testresultId), Duration.Inf)
          }
        }

      } finally {
        db.close()
      }
    }
  }

}
