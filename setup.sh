set -e

for i in {1..10000}; do
  wget --header 'Authorization: *:*.unleash-insecure-admin-api-token' \
      --header 'Content-Type: application/json' \
      --post-data='{
    "type": "release",
    "name": "a_toggle_'$i'",
    "description": "",
    "impressionData": false
  }' \
      --no-check-certificate \
      --output-document - \
      'http://unleash:4242/api/admin/projects/default/features'

  # enable the feature toggle for the development environment
  wget --header 'Authorization: *:*.unleash-insecure-admin-api-token' \
      --no-check-certificate \
      --output-document - \
      --post-data='' \
      'http://unleash:4242/api/admin/projects/default/features/a_toggle_'$i'/environments/development/on'
done


# create a frontent api key and store it in /token/frontend.txt so php SDK can read it
wget --post-data='{
  "username": "frontend-token",
  "type": "FRONTEND",
  "environment": "development",
  "projects": [
    "*"
  ]
}' \
  --header 'Authorization: *:*.unleash-insecure-admin-api-token' \
  --header 'Content-Type: application/json' \
  --no-check-certificate \
  --output-document - \
  'http://unleash:4242/api/admin/api-tokens' | sed 's/.*secret":"\([^"]*\).*/\1/' > /token/frontend.txt
