import scala.concurrent.{Await, Future}
import scala.concurrent.ExecutionContext.Implicits.global
import scala.concurrent.duration.Duration
import scala.util.{Failure, Success, Try}
import scala.util.control.NonFatal




val groupSize = 2
val allUris = List("a", "b", "c", "d", "e")

val results = allUris.grouped(groupSize).flatMap(uriGroup => {
  println(s"running $uriGroup")
  val futures = for (uri <- uriGroup) yield {
    Future {
      println(s"here: $uri")
      if (uri == "c") throw new Exception("fooo!")
      uri.toUpperCase()
    }
  }
  val lifted = futures.map(_.map(Success(_)).recover { case NonFatal(e) => Failure(e)})
  Await.result(Future.sequence(lifted), Duration.Inf)
})

println(results.toList)

Thread.sleep(100)
