SHELL:=/bin/bash
.ONESHELL:

bash:
	@if [[ ! -f /.dockerenv ]]; then
		docker exec -ti -u dev jira_status_notifier_php bash
	else
		echo "You are already into the docker bash.";
	fi

csfix:
	@if [[ -f /.dockerenv ]]; then
		cd /srv/jira-status-notifier && vendor/bin/php-cs-fixer fix
	else
		docker exec -ti -u dev jira_status_notifier_php sh \
			-c "cd /srv/jira-status-notifier && vendor/bin/php-cs-fixer fix"
	fi

# make tests ARGS="--filter AppTest"
tests:
	@if [[ -f /.dockerenv ]]; then
		cd /srv/jira-status-notifier && vendor/bin/phpunit ${ARGS} --coverage-html coverage;
	else
		docker exec -ti -u dev jira_status_notifier_php sh \
			-c "cd /srv/jira-status-notifier && vendor/bin/phpunit $(ARGS) --coverage-html coverage"
	fi

# make composer ARGS="require phpunit/phpunit"
composer:
	@if [[ -f /.dockerenv ]]; then
		cd /srv/jira-status-notifier && composer ${ARGS}
	else
		docker exec -ti -u dev jira_status_notifier_php sh \
			-c "cd /srv/jira-status-notifier && composer $(ARGS)"
	fi

psalm:
	@if [[ -f /.dockerenv ]]; then
		cd /srv/jira-status-notifier && vendor/bin/psalm ${ARGS}
	else
		docker exec -ti -u dev jira_status_notifier_php sh \
			-c "cd /srv/jira-status-notifier && vendor/bin/psalm ${ARGS}"
	fi

psalm-log:
	@if [[ -f /.dockerenv ]]; then
		cd /srv/jira-status-notifier && vendor/bin/psalm --output-format=text --show-info=true > psalm.log
	else
		docker exec -ti -u dev jira_status_notifier_php sh \
			-c "cd /srv/jira-status-notifier && vendor/bin/psalm --output-format=text --show-info=true > psalm.log"
	fi

.PHONY: bash csfix tests composer psalm psalm-log
