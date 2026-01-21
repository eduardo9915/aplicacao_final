<?php
    class Setor {
        private int $idSetor;
        private string $nomeSetor;

        public function __construct(
            $nomeSetor,
            $idSetor = 0
        ) {
            $this->idSetor = $idSetor;
            $this->nomeSetor = $nomeSetor;
        }

        public function getId() {
            return $this->idSetor;
        }

        public function getNome() {
            return $this->nomeSetor;
        }
    }
?>