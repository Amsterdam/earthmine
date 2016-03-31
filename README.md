# atlas_earthmine
Atlas earthmine project

Wanneer je earthmine lokaal wilt draaien:

In de root folder staat een bestand met de volgende environment variabellen:

EARTHMINE_SECRET= 
EARTHMINE_KEY=

Deze keys zijn terug te vinden in het openstack project (atlas_openstack/group_vars/all/passwords.yml)

Vul deze in:

docker-compose build 
docker-compose up -d

