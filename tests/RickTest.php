<?php

use PHPUnit\Framework\TestCase;

class RickTest extends TestCase
{
    // Test para verificar si la respuesta de la API es válida
    public function testApiReturnsValidResponse()
    {
        // Simulando la URL del endpoint
        $url = "https://rickandmortyapi.com/api/character";

        // Haciendo la solicitud GET
        $response = file_get_contents($url);

        // Verificando que la respuesta no sea falsa o vacía
        $this->assertNotFalse($response, "La respuesta de la API no debe ser falsa o vacía");

        // Verificando que la respuesta sea un JSON válido
        $data = json_decode($response, true);
        $this->assertIsArray($data, "La respuesta de la API debe ser un array JSON válido");
    }

    // Test para verificar si la estructura del JSON es correcta
    public function testApiJsonStructure()
    {
        // Simulando la URL del endpoint
        $url = "https://rickandmortyapi.com/api/character";

        // Haciendo la solicitud GET
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        // Verificando que el JSON tiene las claves esperadas
        $this->assertArrayHasKey('results', $data, "El JSON debe contener la clave 'results'");
        $this->assertArrayHasKey('info', $data, "El JSON debe contener la clave 'info'");
    }

    // Test para verificar si se manejan correctamente los datos de los personajes
    public function testCharacterDataProcessing()
    {
        // Simulando un JSON de ejemplo de la API
        $json = '{
            "info": {
                "count": 826,
                "pages": 42,
                "next": "https://rickandmortyapi.com/api/character?page=2",
                "prev": null
            },
            "results": [
                {
                    "id": 1,
                    "name": "Rick Sanchez",
                    "status": "Alive",
                    "species": "Human",
                    "type": "",
                    "gender": "Male",
                    "origin": {
                        "name": "Earth (C-137)",
                        "url": "https://rickandmortyapi.com/api/location/1"
                    },
                    "location": {
                        "name": "Citadel of Ricks",
                        "url": "https://rickandmortyapi.com/api/location/3"
                    },
                    "image": "https://rickandmortyapi.com/api/character/avatar/1.jpeg",
                    "episode": ["https://rickandmortyapi.com/api/episode/1"],
                    "url": "https://rickandmortyapi.com/api/character/1",
                    "created": "2017-11-04T18:48:46.250Z"
                }
            ]
        }';

        $data = json_decode($json, true);

        // Verificando que el primer personaje tenga las claves correctas
        $this->assertArrayHasKey('name', $data['results'][0], "El personaje debe tener un nombre");
        $this->assertEquals('Rick Sanchez', $data['results'][0]['name'], "El nombre del personaje debe ser 'Rick Sanchez'");
    }
}

?>
