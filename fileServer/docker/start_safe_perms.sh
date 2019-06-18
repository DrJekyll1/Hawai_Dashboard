#!/bin/bash

##
# Detect the ownership of the webroot
# and run apache as that user.
#
main() {
    local owner group owner_id group_id tmp

    owner=$(stat -c %U .)
    group=$(stat -c %G .)
    owner_id=$(stat -c %u .)
    group_id=$(stat -c %g .)

    if [ "$owner" != "root" ]; then

      if [ "$owner" = "UNKNOWN" ]; then
          owner=$(randname)
          if [ "$group" = "UNKNOWN" ]; then
              group=$owner
              addgroup --system --gid "$group_id" "$group"
          fi
          adduser --system --uid=$owner_id --gid=$group_id "$owner"
      fi
      tmp=/tmp/$(randname)
      {
          echo "User $owner"
          echo "Group $group"
          grep -v '^User' /etc/apache2/project.conf |
              grep -v '^Group'
      } >> "$tmp" &&
      cat "$tmp" > /etc/apache2/project.conf &&
      rm "$tmp"
      # Not volumes, so need to be chowned
      chown -R "$owner:$group" /var/lock/apache2
      chown -R "$owner:$group" /var/log/apache2
      chown -R "$owner:$group" /var/run/apache2

    fi

}

#
# Generate a random sixteen-character
# string of alphabetical characters
#
randname() {
    cat /dev/urandom | tr -cd 'a-z' | head -c 16
}

main "$@"
