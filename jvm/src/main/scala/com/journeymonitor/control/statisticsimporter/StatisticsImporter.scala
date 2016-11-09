package com.journeymonitor.control.statisticsimporter

import java.io.{ByteArrayInputStream, InputStream, OutputStream}
import java.nio.charset.StandardCharsets
import java.sql.{Date => SqlDate}

import org.apache.http.{Header, HttpEntity, HttpResponse, ProtocolVersion}
import org.apache.http.client.{HttpClient, ResponseHandler}
import org.apache.http.client.methods.HttpGet
import org.apache.http.entity.BasicHttpEntity
import org.apache.http.impl.client.HttpClients
import org.apache.http.message.{BasicHttpResponse, BasicStatusLine}
import slick.lifted.Tag
import slick.driver.SQLiteDriver.api._

import scala.concurrent.Await
import scala.concurrent.duration._
import scala.concurrent.ExecutionContext.Implicits.global
import scala.util.{Failure, Success}

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

  val responseHandler = new ResponseHandler[Unit]() {
    override def handleResponse(response: HttpResponse): Unit = {
      val entity = response.getEntity
      val inputStream: InputStream = entity.getContent()
      val db = Database.forURL("jdbc:sqlite:/var/tmp/journeymonitor-control-test.sqlite3", driver = "org.sqlite.JDBC")
      try {
        val done = inputStreamToStatistics(inputStream) { statisticsModel: StatisticsModel =>
          println(statisticsModel)
          val insertAction = statisticsTable.insertOrUpdate(
            statisticsModel.testresultId,
            statisticsModel.runtimeMilliseconds,
            statisticsModel.numberOf200,
            statisticsModel.numberOf400,
            statisticsModel.numberOf500,
            new SqlDate(System.currentTimeMillis),
            new SqlDate(System.currentTimeMillis)
          )
          db.run(insertAction).onComplete {
            case Success(s) => println("ok: " + s)
            case Failure(t) => println(t.printStackTrace())
          }
        } recover {
          case e => println(e)
        }
        Await.result(done, 10.seconds)
        println("done")
      } finally {
        db.close()
      }
    }
  }

}
