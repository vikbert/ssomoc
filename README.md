<div align="center">
    <h1 style="font-weight: bolder; margin-top: 0px" class="opacity-75">SSOmoc</h1>
</div>

<div align="center">
  <p>SSOmoc demonstrates the most commonly used authentications in `symfony 5` project, such as <code>login form</code>, <code>token authentication</code>, <code>sso via oauth2-fake-server</code>.</p>

  <p>
    <a href="#">
      <img src="https://img.shields.io/badge/PRs-Welcome-brightgreen.svg?style=flat-square" alt="PRs Welcome">
    </a>
    <a href="#">
      <img src="https://img.shields.io/badge/License-Apache-brightgreen.svg?style=flat-square" alt="Apache License">
    </a>
  </p>
</div>

---

![](docs/ssomoc.png)

## Starting started

```
git clone https://bitbucket.schwarz/scm/~zhoux/ssomoc.git
cd ssomoc
make start
```

## Localhost URL

go to [http://localhost](http://localhost)

## Authentication
there are serveral options to authenticate the client in this demo project.
- login `form`
- X-AUTH-TOKEN via `token`
- sso via `Mocked oauth2 server`
- sso via `GitHub Oauth2`

See the details in `LoginFormAuthenticator`, `Oauth2Authenticator`, `TokenAuthenticator`, `GithubAuthenticator` in `src/Security` and the guard configuration in `security.yaml`

<img src="docs/authenticator.png" alt="authenticator" width="600">

### Option 1: 
> authentication via `login form`

classic login form with `email` and `password`: go to `http://localhost/login` and do login with `email` and `password`.

```bash
 # see the credentials in security.aml
 user: ssomoc@mail.com
 pass: ssomoc 
```


### Option 2: 
> authentication via `sso oauth2`

sso via oauth2 (mock API): on the homepage, click on the button `SSO Login`


### Option 3: 
> authentication via `X-AUTH-TOKEN`

It can be tested in curl or in REST client. The `X-AUTH-TOKEN` will be persisted in browser cookie under the name `authToken`, after a successful `sso-login` or `default login`.

```bash
curl -i -X GET \
   -H "X-AUTH-TOKEN:3e5a75e5-c3c1-4e56-9ad5-65657f1afb9c" \
 'http://ssomoc.localhost/api/todos' 
```
### Option 4: 
> authentication vai `GitHub Oauth2`

you need create the `Oauth2 app` in the github account to get the `clientID` and `clientSecret`. 

To do this, go to `github.com` and select `setting` => `developer settings` => `OAuth Apps` => `New OAuth App`.


Then add the generated client ID and client secret to a new file named `.env.local` to overwrite the existing dummy `SSO_CLIENT_ID` and `SSO_CLIENT_SECRET` in `.env`. 

For example:

```
// .env.local
# ###> Oauth API: gitHub ###
SSO_BASE_AUTHORIZATION_URL=http://ssomoc.localhost/api/oauth/mock/authz
SSO_BASE_ACCESS_TOKEN_URL=http://nginx/api/oauth/mock/token
SSO_RESOURCE_OWNER_DETAILS_URL=http://nginx/api/oauth/mock/userinfo
SSO_CLIENT_ID=e5243e17278c99b1b8e9
SSO_CLIENT_SECRET=82496bbd41f3a3e512ef9328c71cf412f8d194d7
###> Oauth API: github ###

###> SIAM Fake Server ###
SIAM_CLIENT_ID=fakeclientid
SIAM_CLIENT_SECRET=fakeclientsecret
SIAM_BASE_AUTHORIZATION_URL=http://localhost:1234/authz
SIAM_BASE_ACCESS_TOKEN_URL=http://siam:1234/token
SIAM_RESOURCE_OWNER_DETAILS_URL=http://siam:1234/userinfo
###> SIAM Fake Server ###

```

## Routing

```bash
➜ docker-compose exec php bin/console debug:router
  -------------------- -------- -------- ------ --------------------------
  Name                 Method   Scheme   Host   Path
 -------------------- -------- -------- ------ --------------------------
  _preview_error       ANY      ANY      ANY    /_error/{code}.{_format}
  app_home             ANY      ANY      ANY    /
  api_github_connect   GET      ANY      ANY    /api/github/connect
  api_github_check     GET      ANY      ANY    /api/github/check
  app_profile          GET      ANY      ANY    /profile
  app_login            ANY      ANY      ANY    /login
  app_logout           ANY      ANY      ANY    /logout
  api_siam_check       GET      ANY      ANY    /api/siam/check
 -------------------- -------- -------- ------ --------------------------
```

## E2E Tests (deprecated)
`login via form`, `login via sso` are tested by cypressE2E tests. To start the tests:
```bash
# be sure that `http://ssomoc.localhost` is reachable.
docker-compose up -d 

npm install
make test

# or
node_modules/cypress/bin/cypress run --spec 'cypress/integration/login_*.spec.js'
```
Test results:
```bash
       Spec                                              Tests  Passing  Failing  Pending  Skipped
  ┌────────────────────────────────────────────────────────────────────────────────────────────────┐
  │ ✔  login_form.spec.js                       00:04        1        1        -        -        - │
  ├────────────────────────────────────────────────────────────────────────────────────────────────┤
  │ ✔  login_sso.spec.js                        00:04        1        1        -        -        - │
  └────────────────────────────────────────────────────────────────────────────────────────────────┘
    ✔  All specs passed!                        00:09        2        2        -        -        -
```


## licence
[apache-2.0](https://choosealicense.com/licenses/apache-2.0/)

## Maintainer
[xun.zhou@mail.schwarz](https://vikbert.github.io/)
