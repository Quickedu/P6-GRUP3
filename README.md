# P6-GRUP3
Sara, Carla, Joel, Edu

# Projecte Clínica de Radiologia

## Descripció del projecte

Aquest projecte consisteix en el desenvolupament d’una aplicació web per a la gestió de cites d’una clínica de radiologia. El sistema permet administrar pacients, treballadors, cites mèdiques i informes radiològics de manera centralitzada. L’objectiu principal és facilitar l’organització interna de la clínica i agilitzar la gestió de les proves radiològiques i la informació mèdica.

---

## Requisits

Per executar el projecte és necessari disposar de:

- Docker
- WSL Ubuntu
- PHP
- Composer
- Node.js i npm
- Laravel Sail
- Git

---

## Instal·lació

```bash

#Clonar el repositori:

git clone git@github.com:ABP-2n-DAW-25-26/P6-GRUP3.git

# Accedir al directori del projecte:

cd P6-GRUP3

# Instal·lar dependències de PHP:
composer install

# Instal·lar dependències de Node:
npm install

# Copiar el fitxer .env:
cp .env.example .env

# Generar la clau de Laravel:
php artisan key:generate

# Iniciar l’entorn amb Sail:
./vendor/bin/sail up -d

# Executar migracions:
./vendor/bin/sail artisan migrate --seed
```

## Configuració de l’entorn

La configuració principal del projecte es realitza mitjançant el fitxer .env, on es defineixen variables com:

- Connexió a la base de dades
- Configuració del servidor
- Credencials dels serveis
- Configuració del correu electrònic

Els serveis principals utilitzats són Docker i Laravel Sail per gestionar l’entorn de desenvolupament de manera contenitzada.

## Execució en desenvolupament

```bash
# Per iniciar el servidor de desenvolupament:
./vendor/bin/sail up

# Executar el frontend amb Vite:
sail composer run dev

# Accedir a l’aplicació des del navegador:
http://localhost
```

## Tecnologies utilitzades

Aquest projecte s’ha desenvolupat utilitzant les següents tecnologies:

- Laravel
- PHP
- Docker
- Vue.js
- Inertia.js
- JavaScript
- Git
- GitHub
- Dinahosting
- Composer
- npm
- WSL
- Ubuntu

## Política d’ús d’IA

Durant el desenvolupament del projecte s’han utilitzat eines d’intel·ligència artificial com a suport per resoldre dubtes tècnics, obtenir exemples de codi, millorar la redacció de textos i agilitzar el procés de desenvolupament. Tot el contingut generat amb IA ha estat revisat, adaptat i validat pels integrants del projecte abans de ser implementat a l’aplicació.

## Estil de codificació

Per mantenir una estructura coherent i facilitar el manteniment del projecte, s’ha seguit majoritàriament l’estil de codificació recomanat per Laravel. Les classes i components utilitzen nomenclatura PascalCase, mentre que les funcions i variables segueixen l’estil camelCase. També s’han respectat convencions de llegibilitat, organització del codi i separació de responsabilitats pròpies de Laravel i Vue.

## Pipeline CI/CD

El projecte disposa d’un sistema de CI/CD implementat mitjançant [GitHub Actions](https://github.com/features/actions?) amb dos workflows principals diferenciats per a les branques `develop` i `main`. Aquests pipelines automatitzen diferents processos del desenvolupament i desplegament de l’aplicació.

En primer lloc, s’executen tasques de comprovació i manteniment del codi, incloent-hi la validació de l’estil amb Laravel Pint, el format del frontend i l’execució del linter per garantir la coherència i qualitat del projecte. També es realitza la instal·lació automàtica de dependències tant de PHP com de Node.js.

El pipeline inclou una fase de tests automatitzats preparada per executar proves del backend i del frontend abans del desplegament. Tot i això, algunes dependències entre fases (`needs`) es troben actualment comentades a causa de problemes externs relacionats amb la configuració de les màquines utilitzades per GitHub Actions durant l’execució dels tests. Aquest problema afectava especialment la connexió amb la base de dades necessària per a les proves automatitzades. Malgrat això, els tests funcionen correctament en local, ja que el projecte utilitza un entorn Docker propi amb la base de dades completament configurada.

Finalment, el sistema realitza el desplegament automàtic de l’aplicació als servidors corresponents quan es produeix un `push` a les branques principals. Durant aquest procés es compila el frontend, es prepara l’entorn de producció, es transfereixen els fitxers al servidor mitjançant SSH i SCP, i s’executen tasques pròpies de Laravel com migracions, optimització de configuració i generació d’enllaços simbòlics per a l’emmagatzematge.

## Documentació AJAX

### `filterPatientDates`

- **Endpoint:** `GET /filter-patient-dates`
- **Nom de ruta:** `filter-patient-dates`
- **Controlador:** `DatesController::filterPatientDates`
- **Per a què serveix:** permet filtrar les cites futures i programades d’un pacient a partir    del seu NTS. S’utilitza des del component de filtre de cites per pacient del panell de secretaria.
- **Dades que espera:** rep el paràmetre `nts` per query string. Exemple: `/filter-patient-dates?nts=ABCD1234567890`
- **Format del NTS:** obligatori, text, amb 4 lletres majúscules seguides de 10 números.
- **Accés:** ruta protegida per usuari autenticat de l’àrea de secretaria.
- **Resposta:** retorna JSON amb `status`, `message` i `data`. Si el pacient existeix, `data` conté les seves cites programades futures amb la informació relacionada del pacient, treballador/metge i tipus de prova. Si no es troba el pacient, retorna `status: "error"` i `data: []`.

## Issues i dificultats del projecte

Durant el desenvolupament del projecte no hem tingut cap problema greu que afectés de manera significativa la planificació ni les dates establertes per completar els diferents objectius de treball. Tot i això, sí que hem hagut de solucionar diverses incidències tècniques pròpies del procés de desenvolupament.

La dificultat més destacable es va produir el dia 20/05/2026, relacionada amb el pipeline de GitHub Actions. Durant aquella jornada es van detectar nombrosos errors en els processos automàtics del projecte. Entre aquests problemes, el sistema de validació d’estil i linting va generar una gran quantitat d’errors relacionats amb el format i l’estructura del codi. A més, els tests automatitzats no aconseguien completar-se correctament a causa de problemes de configuració i connexió amb la base de dades dins de l’entorn d’execució de GitHub Actions.

Finalment, els problemes es van poder mitigar ajustant parcialment el pipeline i mantenint separades algunes fases del procés de CI/CD mentre es continuava el desenvolupament del projecte.


