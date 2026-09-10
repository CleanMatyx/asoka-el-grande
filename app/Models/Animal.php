<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Builder; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\HasMany; use Illuminate\Database\Eloquent\SoftDeletes;
class Animal extends Model {
 use SoftDeletes;
 protected $table = 'animales';
 protected $fillable=['nombre','slug','especie','raza','sexo','fecha_nacimiento','fecha_estimada','tamano','estado','vacunado','con_chip','esterilizado','necesidades_especiales','descripcion_necesidades_especiales','compatible_perros','compatible_gatos','compatible_ninos','descripcion','galeria','fecha_llegada','visualizaciones','meta_titulo','meta_descripcion'];
 protected function casts(): array { return ['fecha_nacimiento'=>'date','fecha_estimada'=>'boolean','fecha_llegada'=>'date','vacunado'=>'boolean','con_chip'=>'boolean','esterilizado'=>'boolean','necesidades_especiales'=>'boolean','compatible_perros'=>'boolean','compatible_gatos'=>'boolean','compatible_ninos'=>'boolean','galeria'=>'array','visualizaciones'=>'integer']; }
 public function solicitudesAdopcion(): HasMany { return $this->hasMany(SolicitudAdopcion::class, 'animal_id'); }
 public function apadrinamientos(): HasMany { return $this->hasMany(Apadrinamiento::class, 'animal_id'); }
 public function scopeAdoptables(Builder $query): Builder { return $query->where('estado','adoptable'); }
 public function scopeInvisibles(Builder $query): Builder { return $query->where('estado','invisible'); }
 public function scopeCasosEspeciales(Builder $query): Builder { return $query->where('estado','caso_especial'); }
 public function scopeFiltrar(Builder $query, array $filtros): Builder { return $query->when($filtros['especie'] ?? null, fn(Builder $q,string $v)=>$q->where('especie',$v))->when($filtros['sexo'] ?? null, fn(Builder $q,string $v)=>$q->where('sexo',$v))->when($filtros['tamano'] ?? null, fn(Builder $q,string $v)=>$q->where('tamano',$v))->when($filtros['estado'] ?? null, fn(Builder $q,string $v)=>$q->where('estado',$v))->when(array_key_exists('necesidades_especiales',$filtros), fn(Builder $q)=>$q->where('necesidades_especiales',(bool)$filtros['necesidades_especiales']))->when($filtros['buscar'] ?? null, fn(Builder $q,string $v)=>$q->where(fn(Builder $s)=>$s->where('nombre','like',"%{$v}%")->orWhere('raza','like',"%{$v}%"))); }
}
