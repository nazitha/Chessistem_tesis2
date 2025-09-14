# Diagrama de Implementaciones de Repositorio

## Diagrama de Relaciones Interface-Implementación

```
┌─────────────────────────────────────────────────────────────────┐
│                    IMPLEMENTACIONES DE REPOSITORIO             │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │       C         │  │       C         │  │       C         │ │
│  │ MemberRepository│  │ UserRepository  │  │ PermissionRepository│ │
│  │   (Eloquent)    │  │   (Eloquent)    │  │   (Eloquent)    │ │
│  └─────────┬───────┘  └─────────┬───────┘  └─────────┬───────┘ │
│            │                    │                    │         │
│            │ implements         │ implements         │ implements│
│            │                    │                    │         │
│  ┌─────────▼───────┐  ┌─────────▼───────┐  ┌─────────▼───────┐ │
│  │       I         │  │       I         │  │       I         │ │
│  │ IMemberRepository│  │ IUserRepository │  │ IPermissionRepository│ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │       C         │  │       C         │  │       C         │ │
│  │ RoleRepository  │  │ AcademyRepository│  │ TournamentRepository│ │
│  │   (Eloquent)    │  │   (Eloquent)    │  │   (Eloquent)    │ │
│  └─────────┬───────┘  └─────────┬───────┘  └─────────┬───────┘ │
│            │                    │                    │         │
│            │ implements         │ implements         │ implements│
│            │                    │                    │         │
│  ┌─────────▼───────┐  ┌─────────▼───────┐  ┌─────────▼───────┐ │
│  │       I         │  │       I         │  │       I         │ │
│  │ IRoleRepository │  │ IAcademyRepository│  │ ITournamentRepository│ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │       C         │  │       C         │  │       C         │ │
│  │ GameRepository  │  │ RoundRepository │  │ ParticipantRepository│ │
│  │   (Eloquent)    │  │   (Eloquent)    │  │   (Eloquent)    │ │
│  └─────────┬───────┘  └─────────┬───────┘  └─────────┬───────┘ │
│            │                    │                    │         │
│            │ implements         │ implements         │ implements│
│            │                    │                    │         │
│  ┌─────────▼───────┐  ┌─────────▼───────┐  ┌─────────▼───────┐ │
│  │       I         │  │       I         │  │       I         │ │
│  │ IGameRepository │  │ IRoundRepository│  │ IParticipantRepository│ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │       C         │  │       C         │  │       C         │ │
│  │ PairingRepository│  │ TierBreakRepository│  │ AnalysisRepository│ │
│  │   (Service)     │  │   (Service)     │  │   (Eloquent)    │ │
│  └─────────┬───────┘  └─────────┬───────┘  └─────────┬───────┘ │
│            │                    │                    │         │
│            │ implements         │ implements         │ implements│
│            │                    │                    │         │
│  ┌─────────▼───────┐  ┌─────────▼───────┐  ┌─────────▼───────┐ │
│  │       I         │  │       I         │  │       I         │ │
│  │ IPairingRepository│  │ ITierBreakRepository│  │ IAnalysisRepository│ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
│                                                                 │
│  ┌─────────────────┐                                            │
│  │       C         │                                            │
│  │ AuditRepository │                                            │
│  │   (Eloquent)    │                                            │
│  └─────────┬───────┘                                            │
│            │                                                    │
│            │ implements                                         │
│            │                                                    │
│  ┌─────────▼───────┐                                            │
│  │       I         │                                            │
│  │ IAuditRepository│                                            │
│  └─────────────────┘                                            │
└─────────────────────────────────────────────────────────────────┘
```

## Explicación del Diagrama

### **¿Qué muestra este diagrama?**

Este diagrama muestra la **relación entre las interfaces de repositorio y sus implementaciones concretas** en el sistema de gestión de torneos de ajedrez.

### **Componentes del Diagrama:**

#### **1. Interfaces (Contratos)**
- **IMemberRepository, IUserRepository, etc.**: Definen qué operaciones se pueden realizar
- Son como "manuales de instrucciones" que especifican los métodos disponibles

#### **2. Implementaciones (Código Real)**
- **MemberRepository, UserRepository, etc.**: Contienen el código real que ejecuta las operaciones
- Son las clases que realmente hacen el trabajo

#### **3. Tipos de Implementación**
- **(Eloquent)**: Usan Eloquent ORM para operaciones CRUD con base de datos
- **(Service)**: Implementan lógica de negocio compleja (emparejamientos, desempates)

### **Relaciones "implements":**

Cada interfaz tiene una implementación concreta que:
- **Cumple el contrato** definido por la interfaz
- **Implementa todos los métodos** especificados en la interfaz
- **Puede ser intercambiada** por otra implementación sin afectar el código que la usa

### **Ventajas de esta arquitectura:**

1. **Flexibilidad**: Se puede cambiar la implementación sin modificar el código que la usa
2. **Testabilidad**: Se pueden crear implementaciones de prueba para testing
3. **Mantenibilidad**: Separación clara entre contrato (interfaz) y implementación
4. **Escalabilidad**: Fácil agregar nuevas implementaciones o modificar existentes

### **Ejemplo de Uso:**

```php
// El código usa la interfaz, no la implementación específica
class TournamentController {
    private ITournamentRepository $tournamentRepo;
    
    public function __construct(ITournamentRepository $repo) {
        $this->tournamentRepo = $repo; // Puede ser cualquier implementación
    }
}
```

Esto permite que el controlador funcione con cualquier implementación que cumpla el contrato de `ITournamentRepository`.
