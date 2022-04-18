package model

type Disciplina struct {
	Id   int    `json:"id"`
	Nome string `json:"nome"`
}

type Perfil struct {
	ID    string `json:"id"`
	Nome  string `json:"nome"`
	Badge int    `json:"badge"`
}
