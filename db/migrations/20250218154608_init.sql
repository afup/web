create table ZZZ__annu_pro
(
    forme_juridique varchar(5)  default '' not null,
    raison_sociale  varchar(50) default '' not null,
    siret           varchar(30) default '' not null,
    email           varchar(60) default '' not null,
    site            varchar(60)            null,
    tel             varchar(30)            null,
    fax             varchar(30)            null,
    adresse1        varchar(50) default '' not null,
    adresse2        varchar(50)            null,
    cp              varchar(10) default '' not null,
    ville           varchar(30) default '' not null,
    pays            varchar(20) default '' not null,
    heb             char(2)                null,
    forfait         char(2)                null,
    regie           char(2)                null,
    formation       char(2)                null,
    conseil         char(2)                null,
    STATUS          varchar(10) default '' not null,
    constraint raison_sociale
        unique (raison_sociale)
)
    engine = MyISAM;

create index nom
    on ZZZ__annu_pro (email);

create table ZZZ__forum2004_inscription
(
    civilite     varchar(4)                                              default ''                    not null,
    id           int auto_increment
        primary key,
    nom          varchar(80)                                             default ''                    not null,
    prenom       varchar(80)                                             default ''                    not null,
    compagnie    varchar(120)                                            default ''                    not null,
    email        varchar(120)                                            default ''                    not null,
    web          varchar(200)                                            default ''                    not null,
    adresse      varchar(255)                                            default ''                    not null,
    codepostal   varchar(5)                                              default ''                    not null,
    ville        varchar(50)                                             default ''                    not null,
    etat         varchar(50)                                             default ''                    not null,
    pays         varchar(50)                                             default ''                    not null,
    achat        varchar(20)                                             default ''                    not null,
    montant      float                                                   default 0                     not null,
    creation     datetime                                                default '0000-00-00 00:00:00' not null,
    modification datetime                                                default '0000-00-00 00:00:00' not null,
    visibilite   enum ('oui', 'non')                                     default 'oui'                 not null,
    afup         enum ('oui', 'non')                                     default 'oui'                 not null,
    nexen        enum ('oui', 'non')                                     default 'oui'                 not null,
    statut       enum ('creation', 'paye', 'refuse', 'annule', 'erreur') default 'creation'            not null,
    commande     varchar(30)                                             default ''                    not null,
    autorisation varchar(10)                                             default ''                    not null,
    transaction  varchar(20)                                             default ''                    not null
)
    engine = MyISAM;

create table ZZZ__forum2005_inscription
(
    civilite     varchar(4)                                              default ''                    not null,
    id           int auto_increment
        primary key,
    nom          varchar(80)                                             default ''                    not null,
    prenom       varchar(80)                                             default ''                    not null,
    compagnie    varchar(120)                                            default ''                    not null,
    email        varchar(120)                                            default ''                    not null,
    web          varchar(200)                                            default ''                    not null,
    adresse      varchar(255)                                            default ''                    not null,
    codepostal   varchar(5)                                              default ''                    not null,
    ville        varchar(50)                                             default ''                    not null,
    etat         varchar(50)                                             default ''                    not null,
    pays         varchar(50)                                             default ''                    not null,
    achat        varchar(20)                                             default ''                    not null,
    montant      float                                                   default 0                     not null,
    creation     datetime                                                default '0000-00-00 00:00:00' not null,
    modification datetime                                                default '0000-00-00 00:00:00' not null,
    visibilite   enum ('oui', 'non')                                     default 'oui'                 not null,
    afup         enum ('oui', 'non')                                     default 'oui'                 not null,
    nexen        enum ('oui', 'non')                                     default 'oui'                 not null,
    statut       enum ('creation', 'paye', 'refuse', 'annule', 'erreur') default 'creation'            not null,
    commande     varchar(30)                                             default ''                    not null,
    autorisation varchar(10)                                             default ''                    not null,
    transaction  varchar(20)                                             default ''                    not null
)
    engine = MyISAM;

create table ZZZ__forumphp
(
    id       tinyint(11) auto_increment,
    societe  varchar(50)            null,
    prenom   varchar(25)            null,
    nom      varchar(25)            null,
    tel      varchar(15)            null,
    email    varchar(50) default '' not null,
    media    varchar(15)            null,
    date     date                   null,
    citation char(3)                null,
    primary key (id, email),
    constraint email
        unique (email),
    constraint email_2
        unique (email)
)
    engine = MyISAM;

create table afup_antenne
(
    id    int auto_increment
        primary key,
    ville varchar(100) not null
)
    collate = utf8mb4_bin;

create table afup_assemblee_generale
(
    date        int(11) unsigned not null,
    description mediumtext       null
)
    collate = utf8mb4_bin;

create table afup_assemblee_generale_question
(
    id         int auto_increment
        primary key,
    date       int(11) unsigned not null,
    label      varchar(255)     not null,
    opened_at  datetime         null,
    closed_at  datetime         null,
    created_at datetime         not null
)
    collate = utf8mb4_bin;

create table afup_badge
(
    id    int auto_increment
        primary key,
    label varchar(255) not null,
    url   varchar(255) not null
)
    collate = utf8mb4_bin;

create table afup_compta_facture
(
    id             int auto_increment
        primary key,
    date_devis     date                              not null,
    numero_devis   varchar(50)                       not null,
    date_facture   date                              null,
    numero_facture varchar(50)                       null,
    societe        varchar(50)                       not null,
    service        varchar(50)                       not null,
    adresse        mediumtext                        not null,
    code_postal    varchar(10)                       not null,
    ville          varchar(50)                       not null,
    id_pays        varchar(10)                       not null,
    email          varchar(100)                      not null,
    tva_intra      varchar(20)                       null,
    observation    mediumtext                        not null,
    ref_clt1       varchar(50)                       not null,
    ref_clt2       varchar(50)                       not null,
    ref_clt3       varchar(50)                       not null,
    nom            varchar(50)                       not null,
    prenom         varchar(50)                       not null,
    tel            varchar(30)                       not null,
    etat_paiement  int                 default 0     not null,
    date_paiement  date                              null,
    devise_facture enum ('EUR', 'DOL') default 'EUR' null
)
    collate = utf8mb4_bin;

create table afup_compta_facture_details
(
    id                    int auto_increment
        primary key,
    idafup_compta_facture int                        not null,
    ref                   varchar(20)                not null,
    designation           varchar(100)               not null,
    quantite              double(11, 2)              not null,
    pu                    double(11, 2)              not null,
    tva                   double(11, 2) default 0.00 null
)
    collate = utf8mb4_bin;

create table afup_conferenciers
(
    conferencier_id            int auto_increment
        primary key,
    id_forum                   smallint    default 0              not null,
    civilite                   varchar(5)  default ''             null,
    nom                        varchar(70) default ''             null,
    prenom                     varchar(50) default ''             null,
    email                      varchar(65) default ''             null,
    societe                    varchar(120)                       null,
    ville                      varchar(255)                       null,
    biographie                 mediumtext                         null,
    twitter                    varchar(255)                       null,
    mastodon                   varchar(255)                       null,
    bluesky                    varchar(255)                       null,
    user_github                int unsigned                       null,
    photo                      varchar(255)                       null,
    will_attend_speakers_diner tinyint(1) unsigned                null,
    has_special_diet           tinyint(1) unsigned                null,
    special_diet_description   mediumtext                         null,
    hotel_nights               set ('before', 'between', 'after') null,
    phone_number               varchar(20)                        null,
    referent_person            varchar(255)                       null,
    referent_person_email      varchar(255)                       null
)
    collate = utf8mb4_bin;

create index id_forum
    on afup_conferenciers (id_forum);

create table afup_conferenciers_sessions
(
    session_id      int           not null,
    conferencier_id int default 0 not null,
    primary key (session_id, conferencier_id)
)
    collate = utf8mb4_bin;

create table afup_contacts
(
    id           bigint auto_increment
        primary key,
    nom          varchar(255)                                                                                        not null,
    prenom       varchar(255)                                                                                        not null,
    email        varchar(255)                                                                                        not null,
    organisation varchar(255)                                                                                        not null,
    poste        varchar(255)                                                                                        not null,
    type         enum ('ssii', 'agence web', 'grand compte', 'presse', 'projet', 'prof', 'sponsor', 'presse NPDC''') not null
)
    collate = utf8mb4_bin;

create table afup_cotisations
(
    id                     smallint unsigned auto_increment
        primary key,
    date_debut             int(11) unsigned     default 0    not null,
    type_personne          tinyint unsigned     default 0    not null,
    id_personne            smallint unsigned    default 0    not null,
    montant                float(6, 2) unsigned default 0.00 not null,
    type_reglement         tinyint unsigned     default 0    null,
    informations_reglement varchar(255)                      null,
    date_fin               int(11) unsigned     default 0    not null,
    numero_facture         varchar(15)          default ''   not null,
    commentaires           mediumtext                        null,
    token                  varchar(255)                      null,
    nombre_relances        tinyint unsigned                  null,
    date_derniere_relance  int(11) unsigned                  null,
    reference_client       varchar(255)                      null
)
    comment 'Cotisation des personnes physiques et morales' collate = utf8mb4_bin;

create index id_personne
    on afup_cotisations (id_personne);

create table afup_email
(
    email     varchar(128) default '' not null
        primary key,
    blacklist tinyint(1)   default 0  not null
)
    collate = utf8mb4_bin;

create index email
    on afup_email (email);

create table afup_facturation_forum
(
    reference              varchar(255)        default '' not null
        primary key,
    montant                float               default 0  not null,
    date_reglement         int(11) unsigned               null,
    type_reglement         tinyint(1) unsigned default 0  not null,
    informations_reglement varchar(255)                   null,
    email                  varchar(100)        default '' not null,
    societe                varchar(40)                    null,
    nom                    varchar(40)                    null,
    prenom                 varchar(40)                    null,
    adresse                mediumtext                     not null,
    code_postal            varchar(10)         default '' not null,
    ville                  varchar(50)         default '' not null,
    id_pays                char(2)             default '' not null,
    autorisation           varchar(20)                    null,
    transaction            varchar(20)                    null,
    etat                   tinyint(1) unsigned default 0  not null,
    facturation            tinyint             default 0  not null,
    id_forum               smallint            default 0  not null,
    date_facture           int(11) unsigned               null
)
    comment 'Facturation pour le forum PHP' collate = utf8mb4_bin;

create index id_forum
    on afup_facturation_forum (id_forum);

create index id_pays
    on afup_facturation_forum (id_pays);

create table afup_forum
(
    id                             smallint auto_increment
        primary key,
    titre                          varchar(50)      default '' not null,
    path                           varchar(100)                null,
    logo_url                       varchar(100)                null,
    nb_places                      int(11) unsigned default 0  not null,
    date_debut                     date                        null,
    date_fin                       date                        null,
    annee                          int                         null,
    text                           mediumtext                  null,
    date_fin_appel_projet          int                         null,
    date_fin_appel_conferencier    int                         null,
    date_fin_vote                  datetime                    null,
    date_fin_prevente              int                         null,
    date_fin_vente                 int                         null,
    date_fin_vente_token_sponsor   int                         null,
    place_name                     varchar(255)                null,
    place_address                  varchar(255)                null,
    date_fin_saisie_repas_speakers int(11) unsigned            null,
    date_fin_saisie_nuites_hotel   int(11) unsigned            null,
    date_annonce_planning          int(11) unsigned            null,
    vote_enabled                   tinyint          default 1  null,
    speakers_diner_enabled         tinyint          default 1  null,
    accomodation_enabled           tinyint          default 1  null,
    waiting_list_url               varchar(255)                null,
    transport_information_enabled  tinyint          default 0  null,
    has_prices_defined_with_vat    tinyint(1)       default 0  not null
)
    collate = utf8mb4_bin;

create table afup_forum_coupon
(
    id       int auto_increment
        primary key,
    id_forum int         not null,
    texte    varchar(45) not null
)
    collate = utf8mb4_bin;

create table afup_forum_partenaires
(
    id                    int auto_increment
        primary key,
    id_forum              int          not null,
    id_niveau_partenariat int          not null,
    ranking               int          not null,
    nom                   varchar(100) not null,
    presentation          mediumtext   null,
    logo                  varchar(100) null,
    site                  varchar(255) null
)
    collate = utf8mb4_bin;

create table afup_forum_planning
(
    id         int auto_increment
        primary key,
    id_session int               null,
    debut      int(10)           null,
    fin        int(10)           null,
    id_salle   smallint(4)       null,
    id_forum   int               null,
    keynote    tinyint default 0 not null
)
    collate = utf8mb4_bin;

create table afup_forum_salle
(
    id       smallint(4) auto_increment
        primary key,
    nom      varchar(255) null,
    id_forum int          null
)
    collate = utf8mb4_bin;

create table afup_forum_sessions_commentaires
(
    id                   int auto_increment
        primary key,
    id_session           int               null,
    id_personne_physique int               null,
    commentaire          longtext          null,
    date                 int(10)           null,
    public               tinyint default 0 null
)
    collate = utf8mb4_bin;

create table afup_forum_special_price
(
    id          int auto_increment
        primary key,
    id_event    int unsigned not null,
    token       varchar(255) not null,
    price       float        null,
    date_start  datetime     not null,
    date_end    datetime     not null,
    description varchar(255) not null,
    created_on  datetime     not null,
    creator_id  int unsigned not null
)
    collate = utf8mb4_bin;

create table afup_forum_sponsor_scan
(
    id                int auto_increment
        primary key,
    sponsor_ticket_id int      not null,
    ticket_id         int      not null,
    created_on        datetime not null,
    deleted_on        datetime null
)
    collate = utf8mb4_bin;

create table afup_forum_sponsors_tickets
(
    id                         int unsigned auto_increment
        primary key,
    company                    varchar(255)               not null,
    token                      varchar(64)                not null,
    contact_email              varchar(255)               not null,
    max_invitations            tinyint unsigned           not null,
    used_invitations           tinyint unsigned default 0 not null,
    id_forum                   int                        not null,
    created_on                 datetime                   not null,
    edited_on                  datetime                   not null,
    creator_id                 int unsigned               not null,
    qr_codes_scanner_available tinyint(1)       default 0 not null,
    constraint token
        unique (token)
)
    collate = utf8mb4_bin;

create table afup_forum_tarif
(
    id                 int unsigned auto_increment
        primary key,
    technical_name     varchar(64)                   not null,
    pretty_name        varchar(255)                  not null,
    public             tinyint(1) unsigned           not null,
    members_only       tinyint(1) unsigned           not null,
    default_price      float                         not null,
    active             tinyint(1)                    not null,
    day                set ('one', 'two')            not null,
    cfp_submitter_only tinyint(1) unsigned default 0 null
)
    collate = utf8mb4_bin;

create table afup_forum_tarif_event
(
    id_tarif    int unsigned  not null,
    id_event    int unsigned  not null,
    price       float         null,
    date_start  datetime      not null,
    date_end    datetime      not null,
    description varchar(1024) null,
    max_tickets int           null,
    primary key (id_tarif, id_event)
)
    collate = utf8mb4_bin;

create index id_event
    on afup_forum_tarif_event (id_event);

create table afup_inscription_forum
(
    id                     int(5) unsigned auto_increment
        primary key,
    date                   int(11) unsigned    default 0  not null,
    reference              varchar(255)        default '' not null,
    coupon                 varchar(255)        default '' null,
    type_inscription       tinyint(1) unsigned default 0  not null,
    montant                float               default 0  not null,
    informations_reglement varchar(255)                   null,
    civilite               varchar(4)          default '' not null,
    nom                    varchar(40)         default '' not null,
    prenom                 varchar(40)         default '' not null,
    email                  varchar(100)        default '' not null,
    telephone              varchar(40)                    null,
    citer_societe          tinyint(1) unsigned default 0  null,
    newsletter_afup        tinyint(1) unsigned default 0  null,
    newsletter_nexen       tinyint(1) unsigned default 0  null,
    commentaires           text                           null,
    etat                   tinyint(1) unsigned default 0  not null,
    facturation            tinyint             default 0  not null,
    id_forum               smallint            default 0  not null,
    id_member              int unsigned                   null,
    member_type            int unsigned                   null,
    special_price_token    varchar(255)                   null,
    mail_partenaire        tinyint(1) unsigned default 0  not null,
    presence_day1          tinyint(1)                     null,
    presence_day2          tinyint(1)                     null,
    nearest_office         varchar(50)                    null,
    transport_mode         smallint                       null,
    transport_distance     smallint                       null,
    qr_code                varchar(10)                    null
)
    comment 'Inscriptions au forum PHP' collate = utf8mb4_bin;

create index id_forum
    on afup_inscription_forum (id_forum);

create index reference
    on afup_inscription_forum (reference);

create table afup_inscriptions_rappels
(
    id       smallint unsigned auto_increment
        primary key,
    email    varchar(255) default '' not null,
    date     int(10)      default 0  not null,
    id_forum smallint     default 0  not null
)
    comment 'Emails pour le rappel du forum PHP' collate = utf8mb4_bin;

create table afup_logs
(
    id                   mediumint unsigned auto_increment
        primary key,
    date                 int(11) unsigned  default 0  not null,
    id_personne_physique smallint unsigned default 0  not null,
    texte                varchar(255)      default '' not null
)
    comment 'Logs des actions' collate = utf8mb4_bin;

create index id_personne_physique
    on afup_logs (id_personne_physique);

create table afup_meetup
(
    id           int          not null
        primary key,
    date         datetime     not null,
    title        varchar(255) null,
    location     varchar(255) null,
    description  text         null,
    antenne_name varchar(255) not null
)
    collate = utf8mb4_bin;

create table afup_niveau_partenariat
(
    id    int auto_increment
        primary key,
    titre varchar(45) not null
)
    collate = utf8mb4_bin;

create table afup_oeuvres
(
    id                   int auto_increment
        primary key,
    id_personne_physique smallint unsigned null,
    categorie            varchar(255)      null,
    valeur               smallint(5)       null,
    date                 int               null
)
    collate = utf8mb4_bin;

create table afup_pays
(
    id  char(2)     default '' not null
        primary key,
    nom varchar(50) default '' not null
)
    comment 'Pays' collate = utf8mb4_bin;

create table afup_personnes_morales
(
    id                     smallint unsigned auto_increment
        primary key,
    civilite               varchar(4)   default '' not null,
    nom                    varchar(40)  default '' not null,
    prenom                 varchar(40)  default '' not null,
    email                  varchar(100) default '' not null,
    raison_sociale         varchar(100) default '' not null,
    siret                  varchar(14)  default '' not null,
    adresse                mediumtext              not null,
    code_postal            varchar(10)  default '' not null,
    ville                  varchar(50)  default '' not null,
    id_pays                char(2)      default '' not null,
    telephone_fixe         varchar(20)             null,
    telephone_portable     varchar(20)             null,
    max_members            tinyint(1) unsigned     null comment 'Nombre maximum de membre autorisé par la cotisation',
    etat                   tinyint(3)   default -1 not null,
    date_relance           int(11) unsigned        null,
    public_profile_enabled tinyint      default 0  null,
    description            text                    null,
    logo_url               varchar(255)            null,
    website_url            varchar(255)            null,
    contact_page_url       varchar(255)            null,
    careers_page_url       varchar(255)            null,
    twitter_handle         varchar(255)            null,
    related_afup_offices   longtext                null,
    membership_reason      varchar(255)            null
)
    comment 'Personnes morales' collate = utf8mb4_bin;

create index pays
    on afup_personnes_morales (id_pays);

create table afup_personnes_morales_invitations
(
    id           int auto_increment
        primary key,
    company_id   int                 not null,
    email        varchar(255)        not null,
    token        varchar(255)        not null,
    manager      tinyint(1) unsigned not null,
    submitted_on datetime            not null,
    status       tinyint(1) unsigned not null
)
    collate = utf8mb4_bin;

create table afup_personnes_physiques
(
    id                          smallint unsigned auto_increment
        primary key,
    id_personne_morale          smallint unsigned default 0  not null,
    login                       varchar(30)       default '' not null,
    mot_de_passe                varchar(32)       default '' not null,
    niveau                      tinyint unsigned  default 0  not null,
    niveau_modules              char(10)          default '' not null,
    roles                       varchar(255)                 not null,
    civilite                    varchar(4)        default '' not null,
    nom                         varchar(40)       default '' not null,
    prenom                      varchar(40)       default '' not null,
    email                       varchar(100)      default '' not null,
    adresse                     mediumtext                   not null,
    code_postal                 varchar(10)       default '' not null,
    ville                       varchar(50)       default '' not null,
    id_pays                     char(2)           default '' not null,
    telephone_fixe              varchar(20)                  null,
    telephone_portable          varchar(20)                  null,
    etat                        tinyint(3)        default -1 not null,
    date_relance                int(11) unsigned             null,
    compte_svn                  varchar(100)                 null,
    nearest_office              varchar(45)                  null,
    slack_invite_status         tinyint           default 0  not null,
    slack_alternate_email       varchar(255)                 null,
    needs_up_to_date_membership tinyint           default 0  null,
    constraint idx_email_unique
        unique (email)
)
    comment 'Personnes physiques' collate = utf8mb4_bin;

create index email
    on afup_personnes_physiques (email);

create index pays
    on afup_personnes_physiques (id_pays);

create index personne_morale
    on afup_personnes_physiques (id_personne_morale);

create table afup_personnes_physiques_badge
(
    afup_personne_physique_id int  not null,
    badge_id                  int  not null,
    issued_at                 date not null,
    primary key (afup_personne_physique_id, badge_id),
    constraint badge_fk
        foreign key (badge_id) references afup_badge (id)
)
    collate = utf8mb4_bin;

create table afup_planete_billet
(
    id                   int auto_increment
        primary key,
    afup_planete_flux_id int          null,
    clef                 varchar(255) null,
    titre                mediumtext   null,
    url                  varchar(255) null,
    maj                  int          null,
    auteur               mediumtext   null,
    resume               mediumtext   null,
    contenu              mediumtext   null,
    etat                 tinyint      null
)
    collate = utf8mb4_bin;

create table afup_planete_flux
(
    id                   int auto_increment
        primary key,
    nom                  varchar(255)      null,
    url                  varchar(255)      null,
    feed                 varchar(255)      null,
    etat                 tinyint           null,
    id_personne_physique smallint unsigned null
)
    collate = utf8mb4_bin;

create table afup_presences_assemblee_generale
(
    id                       int(11) unsigned auto_increment
        primary key,
    id_personne_physique     smallint unsigned             null,
    date                     int(11) unsigned    default 0 not null,
    presence                 tinyint(1) unsigned default 0 not null,
    id_personne_avec_pouvoir smallint unsigned   default 0 not null,
    date_consultation        int(11) unsigned    default 0 null,
    date_modification        int(11) unsigned    default 0 null
)
    collate = utf8mb4_bin;

create table afup_sessions
(
    session_id                                int auto_increment
        primary key,
    id_forum                                  smallint            default 0    not null,
    date_soumission                           date                             not null,
    titre                                     varchar(255)        default ''   not null,
    abstract                                  mediumtext                       not null,
    staff_notes                               mediumtext                       null,
    journee                                   tinyint(1)          default 0    not null,
    genre                                     tinyint(1)          default 1    not null,
    skill                                     tinyint(1)          default 0    not null,
    with_workshop                             tinyint(1)          default 0    not null,
    workshop_abstract                         mediumtext                       null,
    plannifie                                 tinyint(1)                       null,
    needs_mentoring                           tinyint(1)          default 0    not null,
    youtube_id                                varchar(30)                      null,
    video_has_fr_subtitles                    tinyint(1) unsigned default 0    not null,
    video_has_en_subtitles                    tinyint(1) unsigned default 0    not null,
    slides_url                                varchar(255)                     null,
    blog_post_url                             varchar(255)                     null,
    language_code                             varchar(2)          default 'fr' null,
    markdown                                  tinyint(1) unsigned default 0    not null,
    joindin                                   int                              null,
    date_publication                          datetime                         null,
    interview_url                             varchar(255)                     null,
    tweets                                    mediumtext                       null,
    transcript                                mediumtext                       null,
    verbatim                                  mediumtext                       null,
    openfeedback_path                         varchar(255)                     null,
    has_allowed_to_sharing_with_local_offices tinyint(1)          default 0    not null
)
    collate = utf8mb4_bin;

create table afup_sessions_invitation
(
    id           int auto_increment
        primary key,
    talk_id      int              not null,
    state        tinyint unsigned not null,
    submitted_on datetime         not null,
    submitted_by int              not null,
    token        varchar(255)     not null,
    email        varchar(255)     not null,
    constraint talk_id_email
        unique (talk_id, email)
)
    collate = utf8mb4_bin;

create table afup_sessions_note
(
    session_id      int      default 0  not null,
    note            tinyint  default 0  not null,
    salt            char(32) default '' not null,
    date_soumission date                not null,
    primary key (note, session_id, salt)
)
    collate = utf8mb4_bin;

create table afup_sessions_vote
(
    id_personne_physique int        default 0 not null,
    id_session           int        default 0 not null,
    a_vote               tinyint(1) default 0 null,
    primary key (id_session, id_personne_physique)
)
    collate = utf8mb4_bin;

create table afup_sessions_vote_github
(
    id           int unsigned auto_increment
        primary key,
    session_id   int unsigned     not null,
    user         int unsigned     not null,
    comment      mediumtext       null,
    vote         tinyint unsigned not null,
    submitted_on datetime         not null
)
    collate = utf8mb4_bin;

create table afup_site_article
(
    id                   int auto_increment
        primary key,
    id_site_rubrique     int                            null,
    titre                text                           null,
    raccourci            varchar(255)                   null,
    chapeau              longtext                       null,
    contenu              longtext                       null,
    type_contenu         varchar(30) default 'markdown' null,
    position             mediumint                      null,
    date                 int                            null,
    etat                 tinyint                        null,
    id_personne_physique smallint unsigned              null,
    theme                int                            null,
    id_forum             int                            null
)
    collate = utf8mb4_bin;

create table afup_site_feuille
(
    id        int auto_increment
        primary key,
    id_parent int          null,
    nom       varchar(255) null,
    lien      varchar(255) null,
    alt       varchar(255) null,
    position  mediumint    null,
    date      int          null,
    etat      tinyint      null,
    image     varchar(255) null,
    image_alt varchar(255) null,
    patterns  mediumtext   null
)
    collate = utf8mb4_bin;

create table afup_site_rubrique
(
    id                   int auto_increment
        primary key,
    id_parent            int                null,
    nom                  text               null,
    raccourci            varchar(255)       null,
    contenu              longtext           null,
    descriptif           text               null,
    position             mediumint          null,
    date                 int                null,
    etat                 tinyint            null,
    id_personne_physique smallint unsigned  null,
    icone                varchar(255)       null,
    pagination           smallint default 0 not null,
    feuille_associee     int                null
)
    collate = utf8mb4_bin;

create table afup_speaker_suggestion
(
    id              int auto_increment
        primary key,
    event_id        int unsigned not null,
    suggester_email varchar(255) not null,
    suggester_name  varchar(255) not null,
    speaker_name    varchar(255) not null,
    comment         mediumtext   null,
    created_at      datetime     not null
)
    collate = utf8mb4_bin;

create table afup_subscription_reminder_log
(
    id            int unsigned auto_increment
        primary key,
    user_id       int unsigned     not null,
    user_type     tinyint unsigned not null,
    email         varchar(255)     not null,
    reminder_key  varchar(30)      not null,
    reminder_date datetime         not null,
    mail_sent     tinyint unsigned not null
)
    collate = utf8mb4_bin;

create table afup_tags
(
    id                   int auto_increment
        primary key,
    source               varchar(255) null,
    id_source            int          null,
    tag                  varchar(255) null,
    id_personne_physique int          null,
    date                 int(10)      null,
    constraint source
        unique (source, id_source, tag)
)
    collate = utf8mb4_bin;

create table afup_techletter
(
    id                int auto_increment
        primary key,
    sending_date      datetime                      not null,
    techletter        mediumtext                    null,
    sent_to_mailchimp tinyint(1) unsigned default 0 not null,
    archive_url       varchar(255)                  null
)
    collate = utf8mb4_bin;

create table afup_techletter_subscriptions
(
    id                int auto_increment
        primary key,
    user_id           int unsigned not null,
    subscription_date datetime     not null
)
    collate = utf8mb4_bin;

create table afup_techletter_unsubscriptions
(
    id                  int auto_increment
        primary key,
    email               varchar(255) not null,
    unsubscription_date datetime     not null,
    reason              varchar(255) null,
    mailchimp_id        varchar(255) null
)
    collate = utf8mb4_bin;

create table afup_throttling
(
    id         int auto_increment
        primary key,
    ip         bigint unsigned null,
    action     varchar(64)     not null,
    object_id  int unsigned    null,
    created_on datetime        not null
)
    collate = utf8mb4_bin;

create table afup_user_github
(
    id          int unsigned auto_increment
        primary key,
    github_id   int unsigned        not null,
    login       varchar(255)        not null,
    name        varchar(255)        null,
    company     varchar(255)        null,
    profile_url varchar(255)        not null,
    avatar_url  varchar(255)        not null,
    afup_crew   tinyint(1) unsigned not null
)
    collate = utf8mb4_bin;

create table afup_vote_assemblee_generale
(
    afup_assemblee_generale_question_id int auto_increment,
    afup_personnes_physiques_id         smallint unsigned                 not null,
    weight                              int                               not null,
    value                               enum ('oui', 'non', 'abstention') null,
    created_at                          datetime                          not null,
    primary key (afup_assemblee_generale_question_id, afup_personnes_physiques_id),
    constraint const_question
        foreign key (afup_assemblee_generale_question_id) references afup_assemblee_generale_question (id)
)
    collate = utf8mb4_bin;

create table afup_votes
(
    id        int auto_increment
        primary key,
    question  longtext      null,
    lancement int default 0 null,
    cloture   int default 0 null,
    date      int default 0 null
)
    collate = utf8mb4_bin;

create table afup_votes_poids
(
    id_vote              int default 0 not null,
    id_personne_physique int default 0 not null,
    commentaire          longtext      null,
    poids                tinyint       null,
    date                 int           null,
    constraint id_vote
        unique (id_vote, id_personne_physique)
)
    collate = utf8mb4_bin;

create table annuairepro_Activite
(
    ID  int default 0 not null
        primary key,
    Nom varchar(255)  null
)
    collate = utf8mb4_bin;

create table annuairepro_ActiviteMembre
(
    Membre        int default 0          not null,
    Activite      int default 0          not null,
    EstPrincipale enum ('True', 'False') null,
    constraint Membre
        unique (Membre, Activite)
)
    collate = utf8mb4_bin;

create table annuairepro_FormeJuridique
(
    ID  int default 0 not null
        primary key,
    Nom varchar(255)  null
)
    collate = utf8mb4_bin;

create table annuairepro_MembreAnnuaire
(
    ID              int auto_increment
        primary key,
    FormeJuridique  int         default 0  not null,
    RaisonSociale   varchar(255)           null,
    SIREN           varchar(255)           null,
    Email           varchar(255)           null,
    SiteWeb         varchar(255)           null,
    Telephone       varchar(20)            null,
    Fax             varchar(20)            null,
    Adresse         mediumtext             null,
    CodePostal      varchar(5)             null,
    Ville           varchar(255)           null,
    Zone            int         default 0  not null,
    id_pays         varchar(2)             not null,
    NumeroFormateur varchar(255)           null,
    MembreAFUP      tinyint(1)             null,
    Valide          tinyint(1)             null,
    DateCreation    datetime               null,
    TailleSociete   int         default 0  not null,
    Password        varchar(50) default '' not null,
    constraint RaisonSociale
        unique (RaisonSociale)
)
    collate = utf8mb4_bin;

create table annuairepro_MembreAnnuaire_iso
(
    ID              int auto_increment
        primary key,
    FormeJuridique  int         default 0  not null,
    RaisonSociale   varchar(255)           null,
    SIREN           varchar(255)           null,
    Email           varchar(255)           null,
    SiteWeb         varchar(255)           null,
    Telephone       varchar(20)            null,
    Fax             varchar(20)            null,
    Adresse         mediumtext             null,
    CodePostal      varchar(5)             null,
    Ville           varchar(255)           null,
    Zone            int         default 0  not null,
    NumeroFormateur varchar(255)           null,
    MembreAFUP      tinyint(1)             null,
    Valide          tinyint(1)             null,
    DateCreation    datetime               null,
    TailleSociete   int         default 0  not null,
    Password        varchar(50) default '' not null,
    constraint RaisonSociale
        unique (RaisonSociale)
)
    collate = utf8mb4_bin;

create table annuairepro_MembreAnnuaire_seq
(
    id int unsigned auto_increment
        primary key
);

create table annuairepro_TailleSociete
(
    ID  int default 0 not null
        primary key,
    Nom varchar(255)  null
)
    collate = utf8mb4_bin;

create table annuairepro_Zone
(
    ID  int default 0 not null
        primary key,
    Nom varchar(255)  null
)
    collate = utf8mb4_bin;

create table compta
(
    id                        int auto_increment
        primary key,
    idclef                    varchar(20)          null,
    idoperation               tinyint(5)           not null,
    idcategorie               int                  not null,
    date_ecriture             date                 not null,
    numero_operation          varchar(100)         null,
    nom_frs                   varchar(50)          not null,
    tva_intra                 varchar(20)          null,
    tva_zone                  varchar(25)          null,
    montant                   double(11, 2)        not null,
    description               varchar(255)         not null,
    comment                   varchar(255)         null,
    attachment_required       tinyint(1) default 0 null,
    attachment_filename       varchar(255)         null,
    numero                    varchar(50)          not null,
    idmode_regl               tinyint(5)           not null,
    date_regl                 date                 null,
    obs_regl                  varchar(255)         not null,
    idevenement               tinyint(5)           null,
    idcompte                  tinyint(2) default 1 not null,
    montant_ht_soumis_tva_20  double(11, 2)        null,
    montant_ht_soumis_tva_10  double(11, 2)        null,
    montant_ht_soumis_tva_5_5 double(11, 2)        null,
    montant_ht_soumis_tva_0   double(11, 2)        null
)
    collate = utf8mb4_bin;

create table compta_categorie
(
    id                            tinyint(5) auto_increment
        primary key,
    idevenement                   tinyint(5)  null,
    categorie                     varchar(50) not null,
    hide_in_accounting_journal_at datetime    null
)
    collate = utf8mb4_bin;

create table compta_compte
(
    id         tinyint(2) auto_increment
        primary key,
    nom_compte varchar(45) not null
)
    collate = utf8mb4_bin;

create table compta_evenement
(
    id                            tinyint(5) auto_increment
        primary key,
    evenement                     varchar(50) not null,
    hide_in_accounting_journal_at datetime    null
)
    collate = utf8mb4_bin;

create table compta_operation
(
    id        tinyint(5) auto_increment
        primary key,
    operation varchar(50) not null
)
    collate = utf8mb4_bin;

create table compta_periode
(
    id         tinyint(5) auto_increment
        primary key,
    date_debut date       not null,
    date_fin   date       not null,
    verouiller tinyint(1) not null
)
    collate = utf8mb4_bin;

create table compta_regle
(
    id                  tinyint(5) auto_increment
        primary key,
    label               varchar(255) not null,
    `condition`         varchar(255) not null,
    is_credit           tinyint(2)   null,
    vat                 varchar(7)   null,
    category_id         tinyint(5)   null,
    event_id            tinyint(5)   null,
    mode_regl_id        tinyint(5)   null,
    attachment_required tinyint(2)   null
)
    collate = utf8mb4_bin;

create table compta_reglement
(
    id                            tinyint(5) auto_increment
        primary key,
    reglement                     varchar(50) not null,
    hide_in_accounting_journal_at datetime    null
)
    collate = utf8mb4_bin;

create table compta_simulation
(
    id           tinyint(5) auto_increment
        primary key,
    idclef       varchar(20)   not null,
    idcategorie  int           not null,
    montant_theo double(11, 2) not null,
    description  varchar(255)  not null,
    idevenement  tinyint(5)    not null,
    idoperation  tinyint(5)    not null,
    periode      date          not null,
    verouiller   tinyint(1)    not null
)
    collate = utf8mb4_bin;

create table rdv_afup
(
    session      varchar(40)  default ''                    not null,
    date         datetime     default '0000-00-00 00:00:00' not null,
    nom          varchar(120) default ''                    not null,
    prenom       varchar(120) default ''                    not null,
    societe      varchar(120) default ''                    not null,
    email        varchar(120) default ''                    not null,
    telephone    varchar(20)  default ''                    not null,
    valide       tinyint      default 0                     not null,
    transmission tinyint(2)   default 0                     not null
)
    collate = utf8mb4_bin;

create index session
    on rdv_afup (session);

create index valide
    on rdv_afup (valide);

create table scan
(
    id         int auto_increment
        primary key,
    visitor_id int          null,
    url        varchar(255) not null,
    date       datetime     not null
)
    collate = utf8mb4_bin;

create table sessions
(
    sess_id       varbinary(128) not null
        primary key,
    sess_data     blob           not null,
    sess_lifetime int unsigned   not null,
    sess_time     int unsigned   not null
)
    collate = utf8mb4_bin;

create index sessions_sess_lifetime_idx
    on sessions (sess_lifetime);

create table tweet
(
    id         varchar(30) not null
        primary key,
    id_session int         not null,
    created_at datetime    not null
)
    collate = utf8mb4_bin;

create table wikini_acls
(
    page_tag  varchar(50) default '' not null,
    privilege varchar(20) default '' not null,
    list      mediumtext             not null,
    primary key (page_tag, privilege)
)
    collate = utf8mb4_bin;

create table wikini_links
(
    from_tag char(50) default '' not null,
    to_tag   char(50) default '' not null,
    constraint from_tag
        unique (from_tag, to_tag)
)
    collate = utf8mb4_bin;

create index idx_from
    on wikini_links (from_tag);

create index idx_to
    on wikini_links (to_tag);

create table wikini_pages
(
    id         int unsigned auto_increment
        primary key,
    tag        varchar(50)     default ''                    not null,
    time       datetime        default '0000-00-00 00:00:00' not null,
    body       mediumtext                                    not null,
    body_r     mediumtext                                    not null,
    owner      varchar(50)     default ''                    not null,
    user       varchar(50)     default ''                    not null,
    latest     enum ('Y', 'N') default 'N'                   not null,
    handler    varchar(30)     default 'page'                not null,
    comment_on varchar(50)     default ''                    not null
)
    collate = utf8mb4_bin;

create index idx_comment_on
    on wikini_pages (comment_on);

create index idx_latest
    on wikini_pages (latest);

create index idx_tag
    on wikini_pages (tag);

create index idx_time
    on wikini_pages (time);

create fulltext index tag
    on wikini_pages (tag, body);

create table wikini_referrers
(
    page_tag char(50)  default ''                    not null,
    referrer char(150) default ''                    not null,
    time     datetime  default '0000-00-00 00:00:00' not null
)
    collate = utf8mb4_bin;

create index idx_page_tag
    on wikini_referrers (page_tag);

create index idx_time
    on wikini_referrers (time);

create table wikini_users
(
    name            varchar(80)     default ''                    not null
        primary key,
    password        varchar(32)     default ''                    not null,
    email           varchar(50)     default ''                    not null,
    motto           mediumtext                                    not null,
    revisioncount   int unsigned    default 20                    not null,
    changescount    int unsigned    default 50                    not null,
    doubleclickedit enum ('Y', 'N') default 'Y'                   not null,
    signuptime      datetime        default '0000-00-00 00:00:00' not null,
    show_comments   enum ('Y', 'N') default 'N'                   not null
)
    collate = utf8mb4_bin;

create index idx_name
    on wikini_users (name);

create index idx_signuptime
    on wikini_users (signuptime);
