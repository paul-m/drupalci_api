DrupalCI-API
============

## Overview

Provides a front facing API for the DrupalCI project. This allows us to change the specific CI implementations as needed, without changing the API.

## API

Currently the API can only do two things: Start a job, and check on its status.

#### Job
A Job is a CI task sent off to Jenkins or similar test runner. Drupal.org or other process requests that jobs be started, and then the test runner (Jenkins) runs the CI process.

The Job has the following properties:

- id: Assigned when the Job is created by the API implementation.
- created: Timestamp of creation.
- repository: Repository to test against.
- branch: Branch of the repository to check out.
- patch: File name of patch to apply to the branch of the repository.
- status: String indicating build phase.
- result: Pass/fail.
- log: Console output of the build thus far.

`POST [/job]`

Starts a job running. 

4xx response if:
- An ID is sent.
- Properties 'repository' and 'branch' are not sent.

`GET [/job/{id}]`

Query for the record with the given job ID. 404 if the ID does not exist.

#### Proposed extensions

These extensions to the API could be present in a future version.

`PUT [/job/{id}/cancel]`

Stop the job.

`PUT [/job/{id}/restart]`

Restarts the job. Implies cancel. Creates new id.


## Phing

### Installation

We use Phing as a build tool for this project. Please install Phing via the following instructions:

https://github.com/phingofficial/phing#installation

### Usage

We have a single task for this project that runs a series of steps. These range from preparation steps to testing. To run this build run the following command:

```
$ phing
```

## Vagrant

**Still to be implemented properly. Added to this project in an early release form.**

Vagrant is very handy. If you do not run Docker natively the following VM will provide a method for debugging and building and executing of containers locally.

Install Vagrant (1.6.x):

http://www.vagrantup.com/downloads.html
Spin up a VM with Docker with the following command:

```
$ vagrant up
```

## Deployment

Capistrano is a great tool for deployment web applications.

### Install

Capistrano can be installed via bundler (http://bundler.io). Run the following command:

```
bundle install --path vendor/bundle
```

To deploy to the DEV run the following command:

```
$ bundle exec cap dev deploy
```

To deploy to the PROD run the following command:

```
$ bundle exec cap prod deploy
```

## Puppet

### Installation

Puppet and Librarian Puppet can be installed via bundler (http://bundler.io). Check out the following script:

```
puppet/provision.sh
```
