package com.journeymonitor.control.statisticsimporter

import org.apache.http.client.methods.HttpGet
import org.apache.http.impl.client.HttpClients

object Main {
  def main(args: Array[String]): Unit = {

    val si = new StatisticsImporter()
    val httpClient = HttpClients.createDefault()
    val httpGet = new HttpGet("http://service-misc-experiments-1.service.gkh-setu.de:8081/testcases/6E86B147-3F55-4DFB-9695-DFDC3E3F5747/statistics/latest/")
    httpClient.execute(httpGet, si.responseHandler)

  }
}
