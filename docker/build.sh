#!/bin/bash

dockerfiles=(
	"base"
	"frontend"
	"db"
)

for dockerfile in ${dockerfiles[*]}
do
	echo  "Building $dockerfile"
	docker build -t "selenior/$dockerfile" --rm "$dockerfile"
done
