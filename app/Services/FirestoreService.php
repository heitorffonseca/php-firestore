<?php

namespace App\Services;

use Google\Cloud\Core\Exception\GoogleException;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Firestore\DocumentReference;

class FirestoreService
{
    private FirestoreClient $client;
    private string $collection;
    private string $document;

    /**
     * @throws GoogleException
     */
    public function __construct()
    {
        $this->client = new FirestoreClient([
            "keyFilePath" => __DIR__ . "/../../google-firestore.json"
        ]);
    }

    /**
     * set collection name
     *
     * @param string $collectionName
     * @return $this
     */
    public function collection(string $collectionName): FirestoreService
    {
        $this->collection = $collectionName;
        return $this;
    }

    /**
     * set document id
     *
     * @param string $documentId
     * @return $this
     */
    public function document(string $documentId): FirestoreService
    {
        $this->validAttribute('collection');

        $this->document = $documentId;
        return $this;
    }

    /**
     * get all documents
     *
     * @return mixed
     */
    public function getDocuments()
    {
        $this->validAttribute('collection');

        return $this->client
            ->collection($this->collection)
            ->documents();
    }

    /**
     * get document
     *
     * @return DocumentReference|null
     */
    private function getDocument(): ?DocumentReference
    {
        $this->validAttribute('document');

        $collection = $this->client->collection($this->collection);

        if (!$collection->documents()->isEmpty()) {
            return $this->client
                ->collection($this->collection)
                ->document($this->document);
        }

        return null;
    }

    /**
     * get document data or some key
     *
     * @param string $key
     * @return array|mixed|null
     */
    public function getData(string $key = "")
    {
        if (!empty($key)) {
            return $this
                ->getDocument()
                ->snapshot()
                ->get($key);
        }

        return $this
            ->getDocument()
            ->snapshot()
            ->data();
    }

    /**
     * add new document
     *
     * @param array $data
     * @return string
     */
    public function newDocument(array $data): string
    {
        $this->validAttribute('collection');

        return $this->client
            ->collection($this->collection)
            ->add($data)
            ->id();
    }

    /**
     * delete document
     *
     * @param string $documentId
     * @return array
     */
    public function deleteDocument(string $documentId): array
    {
        $this->validAttribute('collection');

        return $this->client
            ->collection($this->collection)
            ->document($documentId)
            ->delete();
    }

    /**
     * update document
     *
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function updateDocument(string $key, $value)
    {
        $this->validAttribute('collection');

        $updateData = [
            "path" => $key,
            "value" => $value
        ];

        $status = $this->client
            ->collection($this->collection)
            ->document($this->document)
            ->update([ $updateData ], [ "merge" => true ]);

        return $status["updateTime"];
    }

    /**
     * valid attribute self
     *
     * @param string $attribute
     * @return void
     */
    private function validAttribute(string $attribute): void
    {
        switch ($attribute) {
            case 'collection':
                !empty($this->collection) || die("Necessário usar a função collection");
                break;

            case 'document':
                !empty($this->document) || die("Necessário usar a função document");
                break;

        }
    }
}
