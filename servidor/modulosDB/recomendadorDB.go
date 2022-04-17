package DB

import (
	"crypto/rand"
	"database/sql"
	"fmt"
	"log"
	"math/big"
	. "server/model"

	_ "github.com/go-sql-driver/mysql"
)

func initDB() *sql.DB {
	db, err := sql.Open("mysql", "root:admin@tcp(127.0.0.1:3306)/addonfp")

	if err != nil {
		log.Fatal(err)
	}

	defer db.Close()
	return db
}

func generateRandom() int {
	nBig, err := rand.Int(rand.Reader, big.NewInt(3))
	if err != nil {
		panic(err)
	}
	n := nBig.Int64()

	return int(n)
}

func GetRecomendados(id string) []Disciplina {
	db := initDB()

	res, err := db.Query(fmt.Sprintf("SELECT * FROM Disciplinas WHERE ID_Tag"+
		"IN ( SELECT ID_Tag FROM Bolha_Recomendados where ID_origem_perfil = %s ) "+
		"AND ID NOT IN (SELECT ID_disciplina FROM Recomendados where ID_origem_perfil = %s ) ORDER BY RAND() LIMIT 10", id, id))

	if err != nil {
		log.Fatal(err)
	}

	defer res.Close()

	var dps []Disciplina
	var Id int
	var Nome string

	for res.Next() {

		err := res.Scan(&Id, &Nome)

		if err != nil {
			log.Fatal(err)
		}

		dps = append(dps, Disciplina{Id: Id, Nome: Nome})

	}
	return dps
}

func GetNaoRecomendados(id string) []Disciplina {
	db := initDB()

	res, err := db.Query(fmt.Sprintf("SELECT Nome FROM Disciplinas WHERE "+
		"ID_Tag NOT IN ( SELECT ID_Tag FROM Bolha_Recomendados where ID_origem_perfil = %s ) AND "+
		"ID NOT IN (SELECT ID_disciplina FROM Recomendados where ID_origem_perfil = %s) ORDER BY RAND() LIMIT 10", id, id))

	if err != nil {
		log.Fatal(err)
	}

	defer res.Close()

	var dps []Disciplina
	var Id int
	var Nome string

	for res.Next() {

		err := res.Scan(&Id, &Nome)

		if err != nil {
			log.Fatal(err)
		}

		dps = append(dps, Disciplina{Id: Id, Nome: Nome})

	}
	return dps
}

func CountCicles() {
	db := initDB()

	res, err := db.Query("SELECT * from Recomendados")

	if err != nil {
		log.Fatal(err)
	}

	var ID int
	var IdP int
	var IdD int
	var cicles int

	for res.Next() {

		err := res.Scan(&ID, &IdP, &IdD, &cicles)

		if err != nil {
			log.Fatal(err)
		}

		if cicles >= 0 {
			//atualiza ciclos
			cicles--
			db.Query(fmt.Sprintf("UPDATE Recomendados SET Ciclos = %d WHERE ID = %d", cicles, ID))
		} else {
			//remove registro
			db.Query(fmt.Sprintf("DELETE from Recomendados WHERE ID = %d", ID))
		}
	}

	defer res.Close()

}

func getCycle(ID_perfil string) int {
	perfil := GetPerfilDB(ID_perfil)

	var cycle [3]int

	if perfil.Badge >= 30 && perfil.Badge < 40 {
		cycle = [3]int{2, 7, 17}
	} else if perfil.Badge >= 40 && perfil.Badge < 80 {
		cycle = [3]int{3, 11, 19}
	} else if perfil.Badge >= 80 && perfil.Badge < 100 {
		cycle = [3]int{5, 13, 23}
	}

	n := generateRandom()

	return cycle[n]
}

func MarcarDisciplinas(ID_perfil string, dps []Disciplina) {
	db := initDB()

	m := getCycle(ID_perfil)

	for _, dp := range dps {
		res, err := db.Query(fmt.Sprintf(`
		IF NOT EXISTS(SELECT * from Recomendados WHERE ID_disciplina = %d)
		BEGIN
			INSERT INTO Recomendados(ID_origem_perfil, ID_disciplina, Ciclos) VALUES(%s, %d, %d)
		END
		`, dp.Id, ID_perfil, dp.Id, m))

		if err != nil {
			log.Fatal(err)
		}

		defer res.Close()
	}

}
