package DB

import (
	"fmt"
	"log"
	. "server/model"
)

func GetMetrics(ID_perfil string) []int {
	db := initDB()

	var metrics []int

	res, err := db.Query(fmt.Sprintf("SELECT * FROM Badge AS b, Perfil AS p WHERE b.ID = p.ID_Badge AND p.ID = %s", ID_perfil))

	if err != nil {
		log.Fatal(err)
	}

	var ID int
	var m1 int
	var m2 int
	var m3 int
	var m4 int

	if res.Next() {
		err := res.Scan(&ID, &m1, &m2, &m3, &m4)

		if err != nil {
			log.Fatal(err)
		}
		metrics = append(metrics, m1, m2, m3, m4)
	}

	return metrics
}

func GetPerfilDB(ID string) Perfil {
	db := initDB()

	res, err := db.Query(fmt.Sprintf("SELECT * FROM Perfil WHERE ID = %s", ID))

	if err != nil {
		log.Fatal(err)
	}

	var Nome string
	var ID_badge int
	var perfil Perfil

	if res.Next() {
		err := res.Scan(&ID, &Nome, &ID_badge)
		metrics := GetMetrics(ID)
		Badge := metrics[0] + metrics[1] + metrics[2] + metrics[3]/4

		perfil = Perfil{ID: ID, Nome: Nome, Badge: Badge}

		if err != nil {
			log.Fatal(err)
		}

	}

	return perfil
}
