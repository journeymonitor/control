type IntToBool = Int => Boolean

def filterBuilder(cb: IntToBool)(i: Int): Option[Int] = {
  if (cb(i)) Some(i) else None
}

val sizeFilter = filterBuilder { fi => fi > 100 } _

sizeFilter(5)
sizeFilter(2000)

val m = Map(1 → "a", 2 → "b")