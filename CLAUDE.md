# CLAUDE.md — Projectinstructies

Dit bestand bevat instructies voor Claude bij het werken in dit project.
Volg deze richtlijnen altijd, tenzij een collega expliciet afwijkt in de chat.

---

## Projectconfiguratie

- **Confluence hoofdpagina ID:** `1178697932`

---

## Confluence paginastructuur

Houd altijd de volgende structuur aan onder de hoofdpagina:

```
📄 [Projectnaam] (hoofdpagina — samenvatting)
├── 📄 Werking: [onderwerp]
├── 📄 Werking: [onderwerp]
├── 📁 Tests
│   ├── 📄 [testnaam]
│   └── 📄 [testnaam]
└── 📁 Cron jobs
    ├── 📄 [jobnaam]
    └── 📄 [jobnaam]
```

### Hoofdpagina
- Geeft een **samenvatting van het project**: doel, context, en voor wie het bedoeld is.
- Bevat een overzicht van de onderliggende pagina's met een korte beschrijving per onderdeel.
- Is de eerste pagina die iemand leest — houd het beknopt en begrijpelijk voor niet-technische collega's.

### Subpagina's — Werking
- Één pagina per functioneel onderdeel of module.
- Beschrijft in detail hoe dat onderdeel werkt, inclusief randgevallen en afhankelijkheden.

### Map — Tests
- Elke testscenario of testgroep krijgt een eigen subpagina.
- Beschrijf wat er getest wordt, hoe, en wat het verwachte resultaat is.

### Map — Cron jobs
- Elke cron job krijgt een eigen subpagina.
- Beschrijf de planning (schedule), wat de job doet, en wat er gebeurt bij een fout.

---

## Documentatie bijwerken (Confluence)

Wanneer een collega vraagt om documentatie te updaten, werk je Confluence-pagina's direct bij via de Atlassian MCP-integratie.

### Werkwijze

1. **Stel eerst vragen als iets onduidelijk is.**
   Ga niet raden. Als niet duidelijk is welke pagina bedoeld wordt, wat er gewijzigd moet worden, of waarom — vraag het dan eerst.

2. **Zoek de juiste pagina op** via de Atlassian MCP voordat je iets aanpast.
   Controleer of je de juiste pagina te pakken hebt en lees de huidige inhoud eerst door.

3. **Schrijf de documentatie in het Nederlands.**
   Zorg dat de tekst leesbaar is voor zowel technische als niet-technische collega's. Vermijd jargon waar mogelijk, of leg het kort uit.

4. **Voeg onderaan de pagina altijd een "Laatst bijgewerkt" sectie toe** met:
    - Wat er gewijzigd is
    - Waarom (kort)

5. **Overschrijf bestaande inhoud niet zomaar.**
   Lees de huidige pagina eerst en behoud relevante informatie. Pas alleen aan wat nodig is.

---

## Algemene werkwijze

- Schrijf altijd in het **Nederlands**.
- Wees consistent: gebruik dezelfde terminologie als de rest van de pagina of het project.
- Pas geen code aan tenzij dit expliciet gevraagd wordt.
- Als je twijfelt over de bedoeling van een wijziging, **vraag het eerst** — documenteer nooit op basis van aannames.

## Hou rekening met de LICENSE
- hou rekening met alle regels van de licentie.