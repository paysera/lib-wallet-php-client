# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 1.26.0
- Added `Paysera_WalletApi_Listener_PayseraUserIpSetter` listener.
- Added `Paysera_WalletApi_OAuth_Consumer::SCOPE_RECENT_STATEMENTS` scope.

## 1.25.0
- Added `Paysera_WalletApi_Client_WalletClient::getTransfers` endpoint.

## 1.24.1
- Added `Paysera_WalletApi_OAuth_Consumer::SCOPE_CHECK_HAS_SUFFICIENT_BALANCE` scope. Required for checking if balance amount is sufficient.

## 1.24.0
### Changed
- `Paysera_WalletApi_OAuth_Consumer::getTransferSignRedirectUri` also requires redirect uri as second argument, adds it's value as redirect_uri query parameter.

## 1.23.0
- Added `getTransferSignRedirectUri` to `Paysera_WalletApi_OAuth_Consumer`

## 1.22.1
### Changed
- Fixed type in `SufficientAmountResponse` mapping.

## 1.22.0
### Added
- `::getTransfer()` method to `WalletClient`.
- `::createTransfer()` method to `WalletClient`.
- `::hasSufficientFunds()` method to `WalletClient`.
- requires `paysera/lib-wallet-transfer-rest-client` to make use of existing entities.`
- bumped minimal required php version by minor `>=5.3 -> >=5.5`

## 1.21.1
### Deprecated
- Deprecated `Paysera_WalletApi_OAuth_Consumer::getResetPasswordUri` and `Paysera_WalletApi_Util_Router::getRemindPasswordUri` methods
