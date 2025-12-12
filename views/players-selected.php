<?php
if (session_status() === PHP_SESSION_NONE) session_start();

/* ----------------------------------------------
   1) PREMIÈRE VISITE → CHOIX DU JOUEUR
-----------------------------------------------*/
if (!isset($_SESSION["role"])) {

    // Choix reçu ? On enregistre puis on recharge cette page
    if (isset($_GET["role"])) {
        $_SESSION["role"] = ($_GET["role"] === "j1") ? "joueur1" : "joueur2";
        $_SESSION["mode"] = "placement";
        header("Location: /Projet_php/views/players-selected.php");
        exit;
    }

    // Page de choix (aucune redirection ici)
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Choix du joueur</title>
        <link rel="stylesheet" href="/Projet_php/CSS/styles_css.css">
        <link rel="stylesheet" href="/Projet_php/CSS/choice_style.css">
    </head>
    <body>
        <h2 style="text-align:center; margin-top:80px;">Choisissez votre rôle</h2>

        <div style="text-align:center; margin-top:40px;">
            <a href="?role=j1"><button>Joueur 1</button></a>
            <a href="?role=j2"><button>Joueur 2</button></a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

/* ----------------------------------------------
   2) ON A UN RÔLE → MODE PLACEMENT
-----------------------------------------------*/
$current = $_SESSION["role"];
$_SESSION["mode"] = "placement";

include($_SERVER['DOCUMENT_ROOT']."/Projet_php/scripts/sql-connect.php");
$sql = new SqlConnect();

/* ------------------------------------
   Liste des bateaux (ID → taille)
-------------------------------------*/
$ships = [
    1 => ["name" => "Porte-avions",      "size" => 5],
    2 => ["name" => "Croiseur",          "size" => 4],
    3 => ["name" => "Contre-torpilleur", "size" => 3],
    4 => ["name" => "Sous-marin",        "size" => 3],
    5 => ["name" => "Torpilleur",        "size" => 2],
];

/* ------------------------------------
   Chargement de la grille du joueur
-------------------------------------*/
$req = $sql->db->prepare("SELECT * FROM $current ORDER BY idgrid ASC");
$req->execute();
$grid = $req->fetchAll(PDO::FETCH_ASSOC);

// Vérification de l'état des deux joueurs
$ready1 = $sql->db->query("SELECT COUNT(*) FROM joueur1 WHERE boat > 0")->fetchColumn() == 17;
$ready2 = $sql->db->query("SELECT COUNT(*) FROM joueur2 WHERE boat > 0")->fetchColumn() == 17;
$bothReady = ($ready1 && $ready2);

/* Bateaux déjà placés */
$boatPlaced = array_fill_keys(array_keys($ships), false);
foreach ($grid as $cell) {
    if ($cell["boat"] > 0) {
        $boatPlaced[(int)$cell["boat"]] = true;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Placement des bateaux</title>
    <link rel="stylesheet" href="/Projet_php/CSS/styles_css.css">
    <link rel="stylesheet" href="/Projet_php/CSS/placement_style.css">

    <style>
        body {
            display: flex;
            justify-content: center;
            gap: 40px;
        }

        .ship-list { width: 220px; }
        .ship {
            background: #444;
            color: white;
            padding: 12px;
            margin: 10px 0;
            cursor: grab;
            text-align: center;
            border-radius: 4px;
            opacity: 1;
            transition: 0.2s;
            font-size: 16px;
        }
        .ship.placed {
            opacity: .35;
            cursor: not-allowed;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(10, 35px);
            gap: 5px;
        }

        .cell {
            width: 35px;
            height: 35px;
            background: #ccc;
            border: 1px solid #333;
            position: relative;
        }

        .cell.ship-cell { background: #666 !important; cursor: pointer; }

        .cell.preview-ok {
            background: rgba(0, 255, 0, 0.45) !important;
        }
        .cell.preview-bad {
            background: rgba(255, 0, 0, 0.45) !important;
        }
    </style>
</head>

<body>

<!-- LISTE DES BATEAUX -->
<div class="ship-list">
    <h3>Bateaux disponibles (<?= htmlspecialchars($current) ?>)</h3>

    <?php foreach ($ships as $id => $info): ?>
        <div class="ship <?= $boatPlaced[$id] ? "placed" : "" ?>"
             draggable="<?= $boatPlaced[$id] ? "false" : "true" ?>"
             data-id="<?= $id ?>"
             data-size="<?= $info["size"] ?>">
            <?= $info["name"] ?> (<?= $info["size"] ?>)
        </div>
    <?php endforeach; ?>

    <p style="font-size:14px; margin-top:20px; opacity:.7;">
        ➤ Cliquez sur un bateau pour changer son orientation<br>
        ➤ Cliquez sur un bateau posé sur la grille pour le retirer
    </p>

    <p style="margin-top:20px;">
        <?php if ($bothReady): ?>
            ✅ Les deux joueurs sont prêts.<br>Vous pouvez lancer la partie.
        <?php else: ?>
            ⏳ En attente que l'autre joueur place ses bateaux...
        <?php endif; ?>
    </p>
</div>

<!-- GRILLE -->
<div class="grid-container" id="grid">
<?php foreach ($grid as $case): ?>
    <div class="cell <?= $case["boat"]>0 ? "ship-cell" : "" ?>"
         data-id="<?= $case['idgrid'] ?>"
         data-boat="<?= $case['boat'] ?>">
    </div>
<?php endforeach; ?>
</div>

<script>
let draggedShip = null;
let shipSize = 0;
let shipId = 0;
let orientation = "H"; // H = horizontal, V = vertical

// Changer orientation
document.querySelectorAll(".ship").forEach(ship => {
    ship.addEventListener("click", () => {
        if (ship.classList.contains("placed")) return;
        orientation = orientation === "H" ? "V" : "H";
    });
});

// Drag start
document.querySelectorAll(".ship").forEach(ship => {
    ship.addEventListener("dragstart", e => {
        if (ship.classList.contains("placed")) {
            e.preventDefault();
            return;
        }
        draggedShip = ship;
        shipSize = parseInt(ship.dataset.size);
        shipId  = parseInt(ship.dataset.id);
    });
});

// Supprimer un bateau posé
document.querySelectorAll(".ship-cell").forEach(cell => {
    cell.addEventListener("click", () => {
        const boat = parseInt(cell.dataset.boat);
        if (boat > 0) {
            fetch("/Projet_php/scripts/place_boat.php", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify({ deleteShip: boat })
            }).then(() => location.reload());
        }
    });
});

function computePositions(start) {
    let pos = [];
    for (let i = 0; i < shipSize; i++) {
        let id = orientation === "H" ? start + i : start + i * 10;

        if (id > 100) return null;

        if (orientation === "H" &&
            Math.floor((start-1)/10) !== Math.floor((id-1)/10))
            return null;

        pos.push(id);
    }
    return pos;
}

function isAdjacentForbidden(positions) {
    const neighbors = [];

    positions.forEach(id => {
        const row = Math.floor((id - 1) / 10);
        const col = (id - 1) % 10;

        const around = [
            [row-1,col-1],[row-1,col],[row-1,col+1],
            [row,  col-1],            [row,  col+1],
            [row+1,col-1],[row+1,col],[row+1,col+1]
        ];

        around.forEach(([r,c]) => {
            if (r>=0 && r<10 && c>=0 && c<10)
                neighbors.push(r*10 + c + 1);
        });
    });

    const cells = document.querySelectorAll(".cell");

    for (let cell of cells) {
        const id = parseInt(cell.dataset.id);
        const boat = parseInt(cell.dataset.boat);

        if (boat > 0 && neighbors.includes(id) && !positions.includes(id)) {
            return true;
        }
    }
    return false;
}

function clearPreview() {
    document.querySelectorAll(".cell")
        .forEach(c => c.classList.remove("preview-ok", "preview-bad"));
}

function showPreview(start) {
    clearPreview();
    let pos = computePositions(start);
    if (!pos) return;

    let bad = isAdjacentForbidden(pos);

    pos.forEach(id => {
        let cell = document.querySelector(`.cell[data-id="${id}"]`);
        if (!cell) return;
        cell.classList.add(bad ? "preview-bad" : "preview-ok");
    });
}

document.querySelectorAll(".cell").forEach(cell => {

    cell.addEventListener("dragover", e => {
        e.preventDefault();
        showPreview(parseInt(cell.dataset.id));
    });

    cell.addEventListener("dragleave", clearPreview);

    cell.addEventListener("drop", () => {
        let start = parseInt(cell.dataset.id);
        let positions = computePositions(start);

        clearPreview();

        if (!positions) return alert("Placement impossible : dépassement !");
        if (isAdjacentForbidden(positions))
            return alert("Impossible : les bateaux ne doivent pas se toucher !");

        sendToServer(shipId, positions);
    });
});

function sendToServer(shipId, positions) {
    fetch("/Projet_php/scripts/place_boat.php", {
        method:"POST",
        headers:{"Content-Type":"application/json"},
        body:JSON.stringify({ shipId, positions })
    }).then(() => location.reload());
}
</script>

<!-- BOUTON COMMENCER LA PARTIE -->
<div style="position:fixed; bottom:40px; left:0; right:0; text-align:center;">
    <?php if ($bothReady): ?>
        <form method="post" action="/Projet_php/scripts/start_battle.php">
            <button style="padding:15px 25px; font-size:20px;">
                ✔ Commencer la partie
            </button>
        </form>
    <?php else: ?>
        <span style="opacity:0.7;">Les deux joueurs doivent avoir placé tous leurs bateaux.</span>
    <?php endif; ?>
</div>

</body>
</html>
