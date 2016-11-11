package com.journeymonitor.control.statisticsimporter

import java.sql.{Date => SqlDate}

import slick.driver.SQLiteDriver.api._
import slick.lifted.Tag

import scala.concurrent.{Await, Future}
import scala.concurrent.duration.Duration
import scala.concurrent.ExecutionContext.Implicits.global
import scala.util.{Failure, Success}

object Main {
  def main(args: Array[String]): Unit = {

    val futures = for (i <- 1 to 100) yield {
      Future {
        if (i > 80) throw new Exception()
        Thread.sleep(1000)
        println(i)
      }
    }

    val lifted = futures.map(_.map(Success(_)).recover { case t: Throwable => Failure(t) })

    val collected = Future.sequence(lifted)

    collected.onComplete {
      case Success(s) =>
        println("OK")
        s.foreach(println)
      case Failure(t: Throwable) =>
        println(t)
    }

    Await.result(collected, Duration.Inf)

  }
}
