#! /bin/bash -l

if [[ "$INSTANCE_NUMBER" != "0" ]]; then
  echo "Instance number is ${INSTANCE_NUMBER}. Stop here."
  exit 0
fi

$@

