package com.journeymonitor.control.statisticsimporter

import java.sql.Date
import java.sql.{Date => SqlDate}

import org.apache.http.impl.client.HttpClients
import slick.driver.SQLiteDriver.api._
import slick.lifted.Tag

import scala.concurrent
import scala.concurrent.{Await, Future}
import scala.concurrent.ExecutionContext.Implicits.global
import scala.concurrent.duration.Duration
import scala.util.{Failure, Success, Try}


object Main {
  def main(args: Array[String]): Unit = {

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

    val db = Database.forURL("jdbc:sqlite:/var/tmp/journeymonitor-control-test.sqlite3", driver = "org.sqlite.JDBC")

    val actions = for (i <- 1 until 100) yield {
      statisticsTable.insertOrUpdate(
        "abc" + i,
        100,
        200,
        400,
        500,
        new SqlDate(System.currentTimeMillis),
        new SqlDate(System.currentTimeMillis)
      )
    }

    val groupedActions = actions.grouped(1).toList

    val res = groupedActions.flatMap(pair => pair.par.map(action =>
      Await.result(db.run(action), Duration.Inf)
    ))

  }
}
