package classes

import (
	. "server/model"
	DB "server/modulosDB"
)

func BuildPerfil(ID string, Nome string, Badge_ID int) *Perfil {
	metrics := DB.GetMetrics(ID)
	Badge := metrics[0] + metrics[1] + metrics[2] + metrics[3]/4

	p := &Perfil{ID: ID, Nome: Nome, Badge: Badge}
	return p
}
