package com.journeymonitor.control.statisticsimporter

import java.io._

import org.apache.http.HttpResponse
import org.apache.http.client.ResponseHandler
import org.apache.http.client.methods.HttpGet
import org.apache.http.impl.client.HttpClients

object Main {
  def main(args: Array[String]): Unit = {

    val fos = new FileOutputStream(new File("./test.iso"))
    val bos = new BufferedOutputStream(fos)

    val rh = new ResponseHandler[Unit]() {
      override def handleResponse(response: HttpResponse): Unit = {
        val entity = response.getEntity
        val inputStream: InputStream = entity.getContent()
        val buffer = new Array[Byte](10240)
        Stream.continually(inputStream.read(buffer)).takeWhile(_ != -1).foreach { _ =>
          bos.write(buffer, 0, 10240)
        }
        bos.close()
      }
    }

    val httpClient = HttpClients.createDefault()
    val httpGet = new HttpGet("http://localhost:4711/")

    httpClient.execute(httpGet, rh)
  }
}
