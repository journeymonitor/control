#!/bin/bash

dockerfiles=(
	"base"
	"frontend"
	"db"
)

for dockerfile in ${dockerfiles[*]}
do
	echo  "Building $dockerfile"
	docker build -t "journeymonitor/$dockerfile" --rm "$dockerfile"
done
