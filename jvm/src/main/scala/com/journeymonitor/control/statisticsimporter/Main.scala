package com.journeymonitor.control.statisticsimporter

object Main {
  def main(args: Array[String]): Unit = {
    new StatisticsImporter().doImport()
  }
}
