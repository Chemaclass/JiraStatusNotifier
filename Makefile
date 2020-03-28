SHELL:=/bin/bash
.ONESHELL:

bash:
	@if [[ ! -f /.dockerenv ]]; then
		docker exec -ti -u dev scrum_master_php bash
	else
		echo "You are already into the docker bash.";
	fi

csfix:
	@if [[ -f /.dockerenv ]]; then
		cd /srv/scrum-master && vendor/bin/php-cs-fixer fix
	else
		docker exec -ti -u dev scrum_master_php sh \
			-c "cd /srv/scrum-master && vendor/bin/php-cs-fixer fix"
	fi

# make tests ARGS="--filter AppTest"
tests:
	@if [[ -f /.dockerenv ]]; then
		cd /srv/scrum-master && vendor/bin/phpunit ${ARGS} --coverage-html coverage;
	else
		docker exec -ti -u dev scrum_master_php sh \
			-c "cd /srv/scrum-master && vendor/bin/phpunit $(ARGS) --coverage-html coverage"
	fi

# make composer ARGS="require phpunit/phpunit"
composer:
	@if [[ -f /.dockerenv ]]; then
		cd /srv/scrum-master && composer ${ARGS}
	else
		docker exec -ti -u dev scrum_master_php sh \
			-c "cd /srv/scrum-master && composer $(ARGS)"
	fi

.PHONY: bash csfix tests composer