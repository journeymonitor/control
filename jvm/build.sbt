version := "1.0-SNAPSHOT"

scalaVersion := "2.11.8"

libraryDependencies ++= Seq(
  "org.apache.httpcomponents" % "httpclient" % "4.5.2",
  "com.fasterxml.jackson.core" % "jackson-core" % "2.8.4",
  "com.typesafe.slick" %% "slick" % "3.2.0-M1", // We cannot use 3.1.1 due to https://github.com/slick/slick/issues/1400
  "org.xerial" % "sqlite-jdbc" % "3.15.1",
  "ch.qos.logback" % "logback-classic" % "1.1.7",
  "com.typesafe.scala-logging" %% "scala-logging" % "3.5.0",
  "org.scalatest" %% "scalatest" % "3.0.0" % "test",
  "com.typesafe" % "config" % "1.3.1"
)

lazy val statisticsImporter = project.in(file("."))
  .settings(assemblyJarName in assembly := "journeymonitor-control-statisticsimporter-assembly.jar")
