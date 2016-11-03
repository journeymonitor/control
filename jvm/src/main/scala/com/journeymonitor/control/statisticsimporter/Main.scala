package com.journeymonitor.control.statisticsimporter

import java.io.{BufferedWriter, File, FileWriter, InputStream}

import org.apache.http.HttpResponse
import org.apache.http.client.ResponseHandler
import org.apache.http.client.methods.HttpGet
import org.apache.http.impl.client.HttpClients

object Main {
  def main(args: Array[String]): Unit = {

    val fileWriter = new FileWriter(new File("./test.txt"))
    // writing each character directly to the file system is very I/O inefficient,
    // thus we use a buffer
    val outputBuffer = new BufferedWriter(fileWriter)

    val rh = new ResponseHandler[Unit]() {
      override def handleResponse(response: HttpResponse): Unit = {
        val entity = response.getEntity
        val inputStream: InputStream = entity.getContent()
        Stream.continually(inputStream.read()).takeWhile(_ != -1).foreach { byte =>
          outputBuffer.write(byte)
        }
        outputBuffer.close()
      }
    }

    val httpClient = HttpClients.createDefault()
    val httpGet = new HttpGet("http://ftp.uni-erlangen.de/mirrors/ubuntu-releases/16.04.1/ubuntu-16.04.1-server-amd64.iso")

    httpClient.execute(httpGet, rh)
  }
}
