# TypoScript Configuration

## Plugin Constants

| Constant               | Path                                            | Description                                                                                                                                                                                                                       | Default |
|------------------------|-------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|---------|
| Storage PID            | `plugin.tx_ats.persistence.storagePid`          | The default storagePid for jobs and users.                                                                                                                                                                                        | none    |
| includeJQuery          | `plugin.tx_ats.settings.includeJQuery`          | The plugin needs jQuery to work properly. Set this to true if you do not already include jQuery in your site.                                                                                                                     | false   |
| loginPage              | `plugin.tx_ats.settings.loginPage`              | Page ID where FE Users can log in. If a user tries to access the application form without being logged in, the plugin will redirect to this page.                                                                                 | none    |
| feUserGroup            | `plugin.tx_ats.settings.feUserGroup`            | The ID of the FE Usergroup all applicants belong to.                                                                                                                                                                              | none    |
| allowedStaticLanguages | `plugin.tx_ats.settings.allowedStaticLanguages` | Applicants can select which languages they speak. With this option, you can limit the available options to a set of `static_languages` uids. Should be a comma-separated list such as `12,30,33`. If not set, all languages are shown. | none    |
| policyPage             | `plugin.tx_ats.settings.policyPage`             | Page ID where your privacy policy is found. The page is linked in the first step of the form where the user has to accept privacy settings.                                                                                       | none    |

## Module Constants

`storagePid`, see above. If the extConf option `enableLegacyBackendTS` is enabled, this setting is inherited from the plugin configuration.
Otherwise you have to set it (`module.tx_ats.persistence.storagePid`).

## Module Settings

If the extConf option `enableLegacyBackendTS` is enabled, all settings are inherited from the plugin configuration and should be set there.

| Setting      | Path                                  | Description                                                                                                                                                     | Default           |
|--------------|---------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------|-------------------|
| deadlineTime | `module.tx_ats.settings.deadlineTime` | The deadline time defines when applications are marked as "deadline exceeded". It reads as "seconds after the jobs endtime is reached". The default is 2 weeks. | 1209600 (2 weeks) |
