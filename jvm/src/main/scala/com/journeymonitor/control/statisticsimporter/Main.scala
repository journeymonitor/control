package com.journeymonitor.control.statisticsimporter

import org.apache.http.impl.client.HttpClients
import slick.driver.SQLiteDriver.api._

import scala.concurrent.{Await, Future}
import scala.concurrent.ExecutionContext.Implicits.global
import scala.concurrent.duration.Duration
import scala.util.Success


object Main {
  def main(args: Array[String]): Unit = {

    var a = 0
    val futures = for (i <- 1 until 10 if a < 5) yield {
      a += 1
      Future {
        println(i)
        Future {
          Thread.sleep(1000)
          "Hello World " + i
        }
      }
    }

    val mf = Future.sequence(futures).map(seq => seq.map { f =>
      f.onComplete {
        case Success(s) => println(s)
      }
      Await.result(f, Duration.Inf)
    })

    mf.onComplete {
      case Success(_) => println("fl " + futures.length)
    }

    Await.result(mf, Duration.Inf)
  }
}
