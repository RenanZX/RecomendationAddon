package main

import (
	"encoding/json"
	"fmt"
	"net/http"

	. "server/model"
	DB "server/modulosDB"

	"github.com/gorilla/handlers"
)

func recommend(w http.ResponseWriter, r *http.Request) {
	id := r.URL.Query().Get("id")

	c1 := make(chan []Disciplina)
	c2 := make(chan []Disciplina)
	go func() {
		c1 <- DB.GetRecomendados(id)
	}()

	go func() {
		c2 <- DB.GetNaoRecomendados(id)
	}()

	var response struct {
		Recomendadas    []Disciplina
		NaoRecomendadas []Disciplina
	}

	response.Recomendadas = <-c1
	response.NaoRecomendadas = <-c2

	go DB.MarcarDisciplinas(id, response.Recomendadas)
	go DB.MarcarDisciplinas(id, response.NaoRecomendadas)

	json.NewEncoder(w).Encode(response)
}

func main() {
	r := http.NewServeMux()

	r.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		fmt.Fprintln(w, "API is runing on port 3001")
	})

	r.HandleFunc("/generate-recomendations", recommend)
	//r.HandleFunc("/v1/integration/createProposal", createProposal)

	credentials := handlers.AllowCredentials()
	methods := handlers.AllowedMethods([]string{"POST", "GET", "PUT", "DELETE", "OPTIONS"})
	origins := handlers.AllowedOrigins([]string{"*"})
	http.ListenAndServe(":3001", handlers.CORS(credentials, methods, origins)(r))
}
