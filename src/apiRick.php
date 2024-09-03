<?php

// URL del endpoint de la API de Rick y Morty
$url = "https://rickandmortyapi.com/api/character";

// Hacer la solicitud GET
$response = file_get_contents($url);

// Convertir la respuesta JSON en un array asociativo de PHP
$data = json_decode($response, true);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personajes de Rick y Morty</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 2em;
            color: #333;
        }
        .search-bar {
            padding: 10px;
            font-size: 16px;
            width: 300px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 250px;
            overflow: hidden;
            text-align: center;
            padding-bottom: 20px;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .card img {
            width: 100%;
            height: auto;
        }
        .card h3 {
            margin: 10px 0;
            font-size: 1.4em;
            color: #333;
        }
        .card p {
            margin: 5px 0;
            color: #555;
            font-size: 0.9em;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination button {
            padding: 10px 15px;
            margin: 0 5px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .pagination button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        /* Estilos para el modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 800px;
            border-radius: 10px;
            text-align: left;
            display: flex;
            flex-direction: row;
            gap: 20px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .modal img {
            border-radius: 8px;
            max-width: 300px;
            width: 100%;
        }
        .modal-details {
            flex-grow: 1;
        }
        .modal-details h2 {
            margin-top: 0;
            font-size: 2em;
        }
        .modal-details p {
            margin: 5px 0;
            font-size: 1em;
            color: #333;
        }
        .modal-details p strong {
            color: #555;
        }
        .modal-episodes {
            margin-top: 20px;
        }
        .modal-episodes h3 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        .modal-episodes ul {
            list-style: none;
            padding: 0;
            max-height: 150px;
            overflow-y: auto;
        }
        .modal-episodes ul li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Personajes de Rick y Morty</h1>
        <input type="text" id="search" class="search-bar" placeholder="Buscar personaje...">
    </div>

    <div class="container" id="characterContainer">
        <?php foreach ($data['results'] as $character): ?>
            <div class="card" onclick="openModal(<?= htmlspecialchars(json_encode($character)) ?>)">
                <img src="<?= $character['image'] ?>" alt="<?= $character['name'] ?>">
                <h3><?= $character['name'] ?></h3>
                <p><strong>Estado:</strong> <?= $character['status'] ?></p>
                <p><strong>Especie:</strong> <?= $character['species'] ?></p>
                <p><strong>Género:</strong> <?= $character['gender'] ?></p>
                <p><strong>Última ubicación:</strong> <?= $character['location']['name'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination">
        <button id="prevBtn" onclick="changePage('prev')" disabled>Anterior</button>
        <button id="nextBtn" onclick="changePage('next')">Siguiente</button>
    </div>

    <!-- Modal -->
    <div id="characterModal" class="modal">
        <div class="modal-content">
            <img id="modalImage" src="" alt="">
            <div class="modal-details">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2 id="modalName"></h2>
                <p><strong>Estado:</strong> <span id="modalStatus"></span></p>
                <p><strong>Especie:</strong> <span id="modalSpecies"></span></p>
                <p><strong>Tipo:</strong> <span id="modalType"></span></p>
                <p><strong>Género:</strong> <span id="modalGender"></span></p>
                <p><strong>Origen:</strong> <span id="modalOrigin"></span></p>
                <p><strong>Última ubicación:</strong> <span id="modalLocation"></span></p>
                <p><strong>Fecha de creación:</strong> <span id="modalCreated"></span></p>
                <div class="modal-episodes">
                    <h3>Episodios</h3>
                    <ul id="modalEpisodes"></ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        let characters = <?= json_encode($data['results']) ?>;
        let currentPage = 1;
        const itemsPerPage = 6;

        function displayCharacters() {
            const container = document.getElementById('characterContainer');
            container.innerHTML = '';

            const filteredCharacters = characters.filter(character => 
                character.name.toLowerCase().includes(document.getElementById('search').value.toLowerCase())
            );

            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedCharacters = filteredCharacters.slice(start, end);

            paginatedCharacters.forEach(character => {
                const card = document.createElement('div');
                card.className = 'card';
                card.onclick = () => openModal(character);
                card.innerHTML = `
                    <img src="${character.image}" alt="${character.name}">
                    <h3>${character.name}</h3>
                    <p><strong>Estado:</strong> ${character.status}</p>
                    <p><strong>Especie:</strong> ${character.species}</p>
                    <p><strong>Género:</strong> ${character.gender}</p>
                    <p><strong>Última ubicación:</strong> ${character.location.name}</p>
                `;
                container.appendChild(card);
            });

            document.getElementById('prevBtn').disabled = currentPage === 1;
            document.getElementById('nextBtn').disabled = end >= filteredCharacters.length;
        }

        async function fetchEpisodeDetails(url) {
            const response = await fetch(url);
            const data = await response.json();
            return data;
        }

        async function openModal(character) {
            document.getElementById('modalName').innerText = character.name;
            document.getElementById('modalImage').src = character.image;
            document.getElementById('modalStatus').innerText = character.status;
            document.getElementById('modalSpecies').innerText = character.species;
            document.getElementById('modalType').innerText = character.type || 'N/A';
            document.getElementById('modalGender').innerText = character.gender;
            document.getElementById('modalOrigin').innerText = character.origin.name;
            document.getElementById('modalLocation').innerText = character.location.name;
            document.getElementById('modalCreated').innerText = new Date(character.created).toLocaleDateString();

            const episodesList = document.getElementById('modalEpisodes');
            episodesList.innerHTML = '';

            const episodePromises = character.episode.map(url => fetchEpisodeDetails(url));
            const episodes = await Promise.all(episodePromises);

            episodes.forEach(episode => {
                const episodeItem = document.createElement('li');
                episodeItem.innerText = `${episode.episode}: ${episode.name} (${new Date(episode.air_date).toLocaleDateString()})`;
                episodesList.appendChild(episodeItem);
            });

            document.getElementById('characterModal').style.display = "block";
        }

        function closeModal() {
            document.getElementById('characterModal').style.display = "none";
        }

        document.getElementById('search').addEventListener('input', displayCharacters);

        displayCharacters();
    </script>

</body>
</html>
