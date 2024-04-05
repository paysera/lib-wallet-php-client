# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 2.6.0
- Added `Paysera_WalletApi_Client_WalletClient::getPermissionsToWallets` endpoint
- Added `Paysera_WalletApi_Entity_ClientPermissionsTpWallet` entity
- Added `Paysera_WalletApi_Mapper::decodeClientPermissionsToWallets` method
- Added `Paysera_WalletApi_Mapper::decodeClientPermissionsToWallet` method
- Added `Paysera_WalletApi_Mapper::encodeClientPermissionsToWallet` method
- Changed `Paysera_WalletApi_Mapper::decodeClient` added decoding `permissions_to_wallets`
- Changed `Paysera_WalletApi_Mapper::encodeClient` added encoding `permissions_to_wallets`
- Added `Paysera_WalletApi_Entity_Client::permissionsToWallets` property
- Added `Paysera_WalletApi_Entity_Client::getPermissionsToWallets` method
- Added `Paysera_WalletApi_Entity_Client::setPermissionsToWallets` method
- Added `Paysera_WalletApi_Entity_Client::addPermissionsToWallet` method

## 2.5.1
### Fixed
- Fixed typo in `Paysera_WalletApi_Mapper::decodeStatementParty`. There was typo for !isset instead of isset when mapping ibans

## 2.5.0
### Added
- Added `Paysera_WalletApi_Entity_Statement::getIbans`
- Added `Paysera_WalletApi_Entity_Statement_Party::getIbans`

## 2.4.2
### Added
- Added `Paysera_WalletApi_Client_WalletClient::getUserConfirmedPhoneNumbers`
- Added `Paysera_WalletApi_Client_TokenRelatedWalletClient::getUserConfirmedPhoneNumbers`

## Removed
- Removed composer.json Authors

## 2.4.1
- Added main_iban field to Account

## 2.4.0
- Added endpoint to revoke access token

## 2.3.2
- Fixed deleteTransferConfiguration endpoint

## 2.3.1
- Added two new endpoints for creating and deleting API configs

## 2.3.0
- Added politically exposed person model
- Added new endpoint to client to get politically exposed person data

## 2.2.1
- Fixed method get_magic_quotes_gpc method usage

## 2.2.0
- Changed keys by how transaction is being encoded to use plain amount instead of cents
- Changed decoding to use decimals instead of cents

## 2.1.0
- Added `\Paysera_WalletApi_Mapper::decodeWalletBalance` mapping from decimal

## 2.0.0
- Changed `checkSignWithPublicKey` sign validation algorithm

## 1.26.0
- Added `Paysera_WalletApi_Listener_AppendHeadersListener` listener.
- Added `Paysera_WalletApi_OAuth_Consumer::SCOPE_RECENT_STATEMENTS` scope.

## 1.25.1
- Fixed a bug where code assumed if location doesn't have any opened hours is opened at all times.


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
