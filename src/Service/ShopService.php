<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

// Un service pour manipuler le contenu de la Boutique
//  qui est composée de catégories et de produits stockés "en dur"
class ShopService
{

    // renvoie toutes les catégories
    public function findAllCategories()
    {
        return $this->categories;
    }

    // renvoie la categorie dont id == $idCategorie
    public function findCategorieById(int $idCategorie)
    {
        $res = array_filter(
            $this->categories,
            function ($c) use ($idCategorie) {
                return $c["id"] == $idCategorie;
            }
        );
        return (sizeof($res) === 1) ? $res[array_key_first($res)] : null;
    }

    // renvoie le produits dont id == $idProduit
    public function findProduitById(int $idProduit)
    {
        $res = array_filter(
            $this->produits,
            function ($p) use ($idProduit) {
                return $p["id"] == $idProduit;
            }
        );
        return (sizeof($res) === 1) ? $res[array_key_first($res)] : null;
    }

    // renvoie tous les produits dont idCategorie == $idCategorie
    public function findProduitsByCategorie(int $idCategorie)
    {
        return array_filter(
            $this->produits,
            function ($p) use ($idCategorie) {
                return $p["idCategorie"] == $idCategorie;
            }
        );
    }

    // renvoie tous les produits dont name ou text contient $search
    public function findProduitsBynameOrtext(string $search)
    {
        return array_filter(
            $this->produits,
            function ($p) use ($search) {
                return ($search == "" || mb_strpos(mb_strtolower($p["name"] . " " . $p["text"]), mb_strtolower($search)) !== false);
            }
        );
    }

    // constructeur du service : injection des dépendances et tris
    public function __construct(RequestStack $requestStack)
    {
        // Injection du service RequestStack
        //  afin de pouvoir récupérer la "locale" dans la requête en cours
        $this->requestStack = $requestStack;
        // On trie le tableau des catégories selon la locale
        usort($this->categories, function ($c1, $c2) {
            return $this->compareSelonLocale($c1["name"], $c2["name"]);
        });
        // On trie le tableau des produits de chaque catégorie selon la locale
        usort($this->produits, function ($c1, $c2) {
            return $this->compareSelonLocale($c1["name"], $c2["name"]);
        });
    }

    ////////////////////////////////////////////////////////////////////////////

    private function compareSelonLocale(string $s1, $s2)
    {
        $collator = new \Collator($this->requestStack->getCurrentRequest()->getLocale());
        return collator_compare($collator, $s1, $s2);
    }

    private $requestStack; // Le service RequestStack qui sera injecté
    // Le catalogue de la boutique, codé en dur dans un tableau associatif
    private $categories = [
        [
            "id" => 1,
            "name" => "Fruits",
            "img" => "img/fruits.jpg",
            "text" => "De la passion ou de ton imagination",
        ],
        [
            "id" => 3,
            "name" => "Junk Food",
            "img" => "img/junk_food.jpg",
            "text" => "Chère et cancérogène, tu es prévenu(e)",
        ],
        [
            "id" => 2,
            "name" => "Légumes",
            "img" => "img/legumes.jpg",
            "text" => "Plus tu en manges, moins tu en es un"
        ],
    ];
    private $produits = [
        [
            "id" => 1,
            "idCategorie" => 1,
            "name" => "Pomme",
            "text" => "Elle est bonne pour la tienne",
            "img" => "img/pommes.jpg",
            "price" => 3.42
        ],
        [
            "id" => 2,
            "idCategorie" => 1,
            "name" => "Poire",
            "text" => "Ici tu n'en es pas une",
            "img" => "img/poires.jpg",
            "price" => 2.11
        ],
        [
            "id" => 3,
            "idCategorie" => 1,
            "name" => "Pêche",
            "text" => "Elle va te la donner",
            "img" => "img/peche.jpg",
            "price" => 2.84
        ],
        [
            "id" => 4,
            "idCategorie" => 2,
            "name" => "Carotte",
            "text" => "C'est bon pour ta vue",
            "img" => "img/carottes.jpg",
            "price" => 2.90
        ],
        [
            "id" => 5,
            "idCategorie" => 2,
            "name" => "Tomate",
            "text" => "Fruit ou Légume ? Légume",
            "img" => "img/tomates.jpg",
            "price" => 1.70
        ],
        [
            "id" => 6,
            "idCategorie" => 2,
            "name" => "Chou Romanesco",
            "text" => "Mange des fractales",
            "img" => "img/romanesco.jpg",
            "price" => 1.81
        ],
        [
            "id" => 7,
            "idCategorie" => 3,
            "name" => "Nutella",
            "text" => "C'est bon, sauf pour ta santé",
            "img" => "img/nutella.jpg",
            "price" => 4.50
        ],
        [
            "id" => 8,
            "idCategorie" => 3,
            "name" => "Pizza",
            "text" => "Y'a pas pire que za",
            "img" => "img/pizza.jpg",
            "price" => 8.25
        ],
        [
            "id" => 9,
            "idCategorie" => 3,
            "name" => "Oreo",
            "text" => "Seulement si tu es un smartphone",
            "img" => "img/oreo.jpg",
            "price" => 2.50
        ],
    ];
}
