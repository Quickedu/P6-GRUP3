import 'vanilla-cookieconsent/dist/cookieconsent.css';
import * as CookieConsent from 'vanilla-cookieconsent';

export function initCookieConsent() {
    CookieConsent.run({
        categories: {
            necessary: {
                enabled: true,
                readOnly: true
            },
            analytics: {}
        },

        language: {
            default: 'ca',
            translations: {
                ca: {
                    consentModal: {
                        title: 'Utilitzem cookies',
                        description: 'Utilitzem cookies per millorar la teva experiencia a la nostra web.',
                        acceptAllBtn: 'Acceptar tot',
                        acceptNecessaryBtn: 'Rebutjar tot',
                        showPreferencesBtn: 'Gestionar preferencies'
                    },
                    preferencesModal: {
                        title: 'Preferencies de cookies',
                        acceptAllBtn: 'Acceptar tot',
                        acceptNecessaryBtn: 'Rebutjar tot',
                        savePreferencesBtn: 'Guardar preferencies',
                        closeIconLabel: 'Tancar',
                        sections: [
                            {
                                title: 'Us de cookies',
                                description: 'Utilitzem cookies per millorar la teva experiencia de navegacio.'
                            },
                            {
                                title: 'Cookies estrictament necessaries',
                                description: 'Aquestes cookies son essencials pel funcionament de la web.',
                                linkedCategory: 'necessary'
                            },
                            {
                                title: "Cookies d'analisi",
                                description: 'Aquestes cookies ens ajuden a entendre com els usuaris interactuen amb la web.',
                                linkedCategory: 'analytics'
                            }
                        ]
                    }
                }
            }
        }
    });
}
