# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

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
