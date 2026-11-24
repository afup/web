#!/usr/bin/env bash
# Usage : behat-shard.sh <numéro du shard, à partir de 1> <nombre de shards de la matrice>
#
# Vérifie que tests/behat/ci-shards découpe la suite Behat sans perte ni doublon (chaque fichier
# .feature de tests/behat/features couvert par exactement un shard, autant de shards que de jobs
# dans la matrice), puis affiche les chemins du shard demandé.
set -euo pipefail

shard_index=$1
shard_total=$2
shards_file=tests/behat/ci-shards
features_dir=tests/behat/features

mapfile -t shards < <(grep -vE '^[[:space:]]*(#|$)' "$shards_file")

status=0

if [ "${#shards[@]}" -ne "$shard_total" ]; then
    echo "::error file=$shards_file::${#shards[@]} shards définis mais $shard_total jobs dans la matrice" >&2
    status=1
fi

while IFS= read -r feature; do
    matches=0
    for shard in "${shards[@]}"; do
        # Découpage volontaire sur les espaces et expansion des globs
        for path in $shard; do
            path=${path%/}
            case "$feature" in
                "$path" | "$path"/*) matches=$((matches + 1)) ;;
            esac
        done
    done
    if [ "$matches" -ne 1 ]; then
        echo "::error file=$shards_file::$feature est couvert par $matches shards (attendu : 1)" >&2
        status=1
    fi
done < <(find "$features_dir" -name '*.feature' | sort)

for shard in "${shards[@]}"; do
    for path in $shard; do
        if [ ! -e "$path" ]; then
            echo "::error file=$shards_file::$path n'existe pas" >&2
            status=1
        fi
    done
done

if [ "$status" -ne 0 ]; then
    exit "$status"
fi

echo "${shards[$((shard_index - 1))]}"
