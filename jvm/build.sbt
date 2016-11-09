version := "1.0-SNAPSHOT"

scalaVersion := "2.11.8"

libraryDependencies ++= Seq(
  "org.apache.httpcomponents" % "httpclient" % "4.5.2",
  "com.fasterxml.jackson.core" % "jackson-core" % "2.8.4",
  "com.typesafe.slick" %% "slick" % "3.2.0-M1", // We cannot use 3.1.1 due to https://github.com/slick/slick/issues/1400
  "org.xerial" % "sqlite-jdbc" % "3.15.1",
  "org.slf4j" % "slf4j-nop" % "1.6.4",
  "org.scalatest" %% "scalatest" % "3.0.0" % "test"
)
