package com.journeymonitor.control.statisticsimporter

import com.typesafe.config.ConfigFactory
import com.typesafe.scalalogging.Logger
import org.apache.http.client.methods.HttpGet
import org.apache.http.impl.client.HttpClients
import slick.lifted.Tag
import slick.jdbc.SQLiteProfile.api._

import scala.concurrent.duration.Duration
import scala.concurrent.{Await, Future}
import scala.concurrent.ExecutionContext.Implicits.global
import scala.util.{Failure, Success}
import scala.util.control.NonFatal

object Main {
  def main(args: Array[String]): Unit = {

    val logger = Logger[this.type]

    val groupSize = 4

    val config = ConfigFactory.load()
    val apiBaseUri = config.getString("endpoint.analyze.api")

    val db = Database.forURL(s"jdbc:sqlite:${config.getString("db.sqlite.path")}", driver = "org.sqlite.JDBC")

    class TestcaseTable(tag: Tag) extends Table[(String, String, String, String, String, Boolean, String, Option[String], Option[String])](tag, "testcase") {
      def id = column[String]("id", O.PrimaryKey)
      def userId = column[String]("user_id")
      def title = column[String]("title")
      def cadence = column[String]("cadence")
      def script = column[String]("script")
      def enabled = column[Boolean]("enabled")
      def createdAt = column[String]("created_at")
      def activatedAt = column[Option[String]]("activated_at")
      def updatedAt = column[Option[String]]("updated_at")
      def * = (id, userId, title, cadence, script, enabled, createdAt, activatedAt, updatedAt)
    }
    val testcaseTable = TableQuery[TestcaseTable]

    val testcaseIds = Await.result(db.run(testcaseTable.map(_.id).result), Duration.Inf)
    db.close()

    val allUris = testcaseIds.map(testcaseId =>
      s"${apiBaseUri}testcases/${testcaseId}/statistics/latest/"
    )

    val results = allUris.grouped(groupSize).toList.flatMap(uriGroup => {
      logger.info(s"Initiating work on: ${uriGroup.mkString(", ")}")
      val futures = for (uri <- uriGroup) yield {
        Future {
          val si = new StatisticsImporter(uri)
          val httpClient = HttpClients.createDefault()
          val httpGet = new HttpGet(uri)
          httpClient.execute(httpGet, si.responseHandler)
          s"Finished import of URI $uri"
        }
      }
      val lifted = futures.map(_.map(Success(_)).recover { case NonFatal(e) => Failure(e)})
      Await.result(Future.sequence(lifted), Duration.Inf)
    })

    val errorCount = results.foldLeft(0)((acc, cur) => cur match { case Failure(_) => acc + 1; case _ => acc + 0})
    if (errorCount > 0) {
      logger.warn(s"All done, but errors occured for ${if (results.length == errorCount) "all" else "some"} testcases:")
      results.foreach {
        case Failure(t) =>
          logger.warn(t.getMessage)
          logger.debug(t.getStackTrace.mkString(" "))
          if (null != t.getCause) {
            logger.warn(t.getCause.getMessage)
            logger.debug(t.getCause.getStackTrace.mkString(" "))
          }
        case _ => ()
      }
      System.exit(1)
    } else {
      logger.info("All done, no errors occured.")
    }

  }
}
