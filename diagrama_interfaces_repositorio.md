# Diagrama de Interfaces de Repositorio - Chessistem_tesis2

## Arquitectura del Sistema

Este proyecto utiliza una arquitectura híbrida que combina:
- **Modelos Eloquent** para acceso a datos
- **Servicios** para lógica de negocio
- **Conexiones PDO directas** para consultas específicas

## Diagrama de Interfaces de Repositorio

```
┌─────────────────────────────────────────────────────────────────┐
│                    INTERFACES DE REPOSITORIO                   │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │ IMiembroRepository│  │ ITorneoRepository│  │ IUserRepository│ │
│  │   GetAllMembers()│  │   GetAllTournaments()│  │   GetAllUsers()│ │
│  │   GetMemberById()│  │   GetTournamentById()│  │   GetUserById()│ │
│  │   CreateMember() │  │   CreateTournament()│  │   CreateUser() │ │
│  │   UpdateMember() │  │   UpdateTournament()│  │   UpdateUser() │ │
│  │   DeleteMember() │  │   DeleteTournament()│  │   DeleteUser() │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │ IAcademiaRepository│  │ IPartidaRepository│  │ IRoleRepository│ │
│  │   GetAllAcademies()│  │   GetAllGames()│  │   GetAllRoles()│ │
│  │   GetAcademyById()│  │   GetGameById()│  │   GetRoleById()│ │
│  │   CreateAcademy()│  │   CreateGame()  │  │   CreateRole() │ │
│  │   UpdateAcademy()│  │   UpdateGame()  │  │   UpdateRole() │ │
│  │   DeleteAcademy()│  │   DeleteGame()  │  │   DeleteRole() │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │ IParticipanteRepository│  │ IRondaRepository│  │ IPermissionRepository│ │
│  │   GetAllParticipants()│  │   GetAllRounds()│  │   GetAllPermissions()│ │
│  │   GetParticipantById()│  │   GetRoundById()│  │   GetPermissionById()│ │
│  │   CreateParticipant()│  │   CreateRound()│  │   CreatePermission()│ │
│  │   UpdateParticipant()│  │   UpdateRound()│  │   UpdatePermission()│ │
│  │   DeleteParticipant()│  │   DeleteRound()│  │   DeletePermission()│ │
│  │   UpdatePoints() │  │                 │  │                 │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │ IAuditRepository│  │ IPairingService │  │ ITiebreakService│ │
│  │   GetAllAudits()│  │   GenerateSwissPairings()│  │   CalculateBuchholz()│ │
│  │   GetAuditById()│  │   GenerateTeamPairings()│  │   CalculateSonnebornBerger()│ │
│  │   CreateAudit() │  │   GenerateRoundRobin()│  │   CalculateProgressive()│ │
│  │                 │  │   OptimizePairings()│  │   CalculateDirectEncounter()│ │
│  │                 │  │   ValidatePairings()│  │   CalculateAverageRating()│ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │ IAnalisisPartidaRepository│  │                 │  │                 │ │
│  │   GetAllGameAnalyses()│  │                 │  │                 │ │
│  │   GetGameAnalysisById()│  │                 │  │                 │ │
│  │   CreateGameAnalysis()│  │                 │  │                 │ │
│  │   UpdateGameAnalysis()│  │                 │  │                 │ │
│  │   DeleteGameAnalysis()│  │                 │  │                 │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

## Diagrama de Implementaciones de Interfaces

```
┌─────────────────────────────────────────────────────────────────┐
│                    IMPLEMENTACIONES DE REPOSITORIO             │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │ MemberRepository│  │ UserRepository  │  │ PermissionRepository│ │
│  │ (Eloquent)      │  │ (Eloquent)      │  │ (Eloquent)      │ │
│  │                 │  │                 │  │                 │ │
│  │ • GetAllMembers()│  │ • GetAllUsers()│  │ • GetAllPermissions()│ │
│  │ • GetMemberById()│  │ • GetUserById()│  │ • GetPermissionById()│ │
│  │ • CreateMember()│  │ • CreateUser() │  │ • CreatePermission()│ │
│  │ • UpdateMember()│  │ • UpdateUser() │  │ • UpdatePermission()│ │
│  │ • DeleteMember()│  │ • DeleteUser() │  │ • DeletePermission()│ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │ RoleRepository  │  │ AcademyRepository│  │ TournamentRepository│ │
│  │ (Eloquent)      │  │ (Eloquent)      │  │ (Eloquent)      │ │
│  │                 │  │                 │  │                 │ │
│  │ • GetAllRoles() │  │ • GetAllAcademies()│  │ • GetAllTournaments()│ │
│  │ • GetRoleById() │  │ • GetAcademyById()│  │ • GetTournamentById()│ │
│  │ • CreateRole()  │  │ • CreateAcademy()│  │ • CreateTournament()│ │
│  │ • UpdateRole()  │  │ • UpdateAcademy()│  │ • UpdateTournament()│ │
│  │ • DeleteRole()  │  │ • DeleteAcademy()│  │ • DeleteTournament()│ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │ GameRepository  │  │ RoundRepository │  │ ParticipantRepository│ │
│  │ (Eloquent)      │  │ (Eloquent)      │  │ (Eloquent)      │ │
│  │                 │  │                 │  │                 │ │
│  │ • GetAllGames() │  │ • GetAllRounds()│  │ • GetAllParticipants()│ │
│  │ • GetGameById() │  │ • GetRoundById()│  │ • GetParticipantById()│ │
│  │ • CreateGame()  │  │ • CreateRound() │  │ • CreateParticipant()│ │
│  │ • UpdateGame()  │  │ • UpdateRound() │  │ • UpdateParticipant()│ │
│  │ • DeleteGame()  │  │ • DeleteRound() │  │ • DeleteParticipant()│ │
│  │                 │  │                 │  │ • UpdatePoints() │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │ PairingRepository│  │ TierBreakRepository│  │ AnalysisRepository│ │
│  │ (Service)       │  │ (Service)       │  │ (Eloquent)      │ │
│  │                 │  │                 │  │                 │ │
│  │ • GenerateSwiss()│  │ • CalculateBuchholz()│  │ • GetAllAnalyses()│ │
│  │ • GenerateTeam()│  │ • CalculateSonnebornBerger()│  │ • GetAnalysisById()│ │
│  │ • GenerateRoundRobin()│  │ • CalculateProgressive()│  │ • CreateAnalysis()│ │
│  │ • OptimizePairings()│  │ • CalculateDirectEncounter()│  │ • UpdateAnalysis()│ │
│  │ • ValidatePairings()│  │ • CalculateAverageRating()│  │ • DeleteAnalysis()│ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
│                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐ │
│  │ AuditRepository │  │                 │  │                 │ │
│  │ (Eloquent)      │  │                 │  │                 │ │
│  │                 │  │                 │  │                 │ │
│  │ • GetAllAudits()│  │                 │  │                 │ │
│  │ • GetAuditById()│  │                 │  │                 │ │
│  │ • CreateAudit() │  │                 │  │                 │ │
│  │                 │  │                 │  │                 │ │
│  │                 │  │                 │  │                 │ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

## Explicación del Diagrama de Implementaciones

### **¿Qué es este diagrama?**

Este diagrama muestra las **implementaciones concretas** de las interfaces de repositorio. Mientras que las interfaces definen "qué se puede hacer", las implementaciones definen "cómo se hace".

### **Tipos de Implementaciones:**

#### **Implementaciones Eloquent (CRUD Tradicional)**
- **MemberRepository, UserRepository, PermissionRepository, RoleRepository**: Implementan operaciones CRUD usando Eloquent ORM
- **AcademyRepository, TournamentRepository, GameRepository**: Manejan entidades principales del sistema
- **RoundRepository, ParticipantRepository**: Gestionan rondas y participantes de torneos
- **AnalysisRepository, AuditRepository**: Manejan análisis de partidas y auditoría

#### **Implementaciones Service (Lógica de Negocio)**
- **PairingRepository**: Implementa lógica para generar emparejamientos automáticamente
- **TierBreakRepository**: Implementa cálculos de sistemas de desempate

### **¿Por qué diferentes tipos de implementación?**

1. **Eloquent**: Para operaciones CRUD estándar con base de datos
2. **Service**: Para lógica compleja que no es solo CRUD (emparejamientos, desempates)

### **Ventajas de esta arquitectura:**

- **Flexibilidad**: Se pueden cambiar implementaciones sin afectar el código que las usa
- **Testabilidad**: Se pueden crear implementaciones de prueba
- **Mantenibilidad**: Cada implementación se enfoca en su responsabilidad específica

### **¿Qué son las interfaces de repositorio?**

Las interfaces de repositorio son como **"manuales de instrucciones"** que dicen:
- Qué operaciones se pueden hacer con cada entidad
- Qué información se puede buscar, crear, actualizar o eliminar
- Cómo debe comportarse cada repositorio

### **Interfaces de Repositorio:**

#### **Entidades Principales con CRUD Completo**
- **IMiembroRepository**: Define operaciones con jugadores (GetAllMembers, GetMemberById, CreateMember, UpdateMember, DeleteMember)
- **IAcademiaRepository**: Define operaciones con academias de ajedrez (CRUD completo)
- **IUserRepository**: Define operaciones con usuarios del sistema (CRUD completo)
- **IRoleRepository**: Define operaciones con roles de usuario (CRUD completo)
- **IPermissionRepository**: Define operaciones con permisos (CRUD completo)

#### **Torneos y Partidas**
- **ITorneoRepository**: Define operaciones con torneos (CRUD completo)
- **IPartidaRepository**: Define operaciones con partidas individuales (CRUD completo)
- **IRondaRepository**: Define operaciones con rondas de torneo (CRUD completo)
- **IParticipanteRepository**: Define operaciones con participantes (CRUD completo + UpdatePoints para actualizar puntuaciones)

#### **Sistema de Auditoría**
- **IAuditRepository**: Define operaciones con auditoría (solo GetAllAudits, GetAuditById, CreateAudit - no se editan ni eliminan)

#### **Sistema de Emparejamiento y Desempate**
- **IPairingService**: Define operaciones para generar emparejamientos automáticamente (GenerateSwissPairings, GenerateTeamPairings, GenerateRoundRobin, OptimizePairings, ValidatePairings)
- **ITiebreakService**: Define operaciones para calcular sistemas de desempate automáticamente (CalculateBuchholz, CalculateSonnebornBerger, CalculateProgressive, CalculateDirectEncounter, CalculateAverageRating)

#### **Análisis de Partidas**
- **IAnalisisPartidaRepository**: Define operaciones con análisis de partidas (CRUD completo)

### **¿Por qué son importantes estas interfaces?**

1. **Organización**: Cada interfaz se enfoca en un tipo específico de información
2. **Flexibilidad**: Se pueden crear diferentes implementaciones (Eloquent, PDO, etc.)
3. **Mantenibilidad**: Es fácil encontrar y modificar operaciones específicas
4. **Testabilidad**: Se pueden crear versiones de prueba para cada interfaz

## Patrones de Acceso a Datos Identificados

### 1. **Patrón Eloquent ORM (Principal)**
```php
// Ejemplo: MiembroController
$miembros = Miembro::with(['usuario.rol', 'ciudad.departamento.pais', 'academia'])
    ->orderBy('cedula')
    ->get();
```

### 2. **Patrón Service Layer**
```php
// Ejemplo: SwissPairingService
class SwissPairingService {
    public function generarEmparejamientos(RondaTorneo $ronda): array
    // Lógica compleja de emparejamiento
}
```

### 3. **Patrón PDO Directo**
```php
// Ejemplo: FederacionController
$conexion = $this->getConexion();
$consulta = "SELECT acronimo, nombre_federacion FROM federaciones";
$resultado = $conexion->prepare($consulta);
```

## Interfaces de Repositorio Conceptuales

### **IMiembroRepository**
```php
interface IMiembroRepository {
    public function findById(string $cedula): ?Miembro;
    public function findByEmail(string $email): ?Miembro;
    public function getActiveMembers(): Collection;
    public function getMembersByAcademia(int $academiaId): Collection;
    public function create(array $data): Miembro;
    public function update(string $cedula, array $data): bool;
    public function delete(string $cedula): bool;
}
```

### **ITorneoRepository**
```php
interface ITorneoRepository {
    public function findById(int $id): ?Torneo;
    public function getActiveTournaments(): Collection;
    public function getTournamentsByOrganizer(string $organizadorId): Collection;
    public function create(array $data): Torneo;
    public function update(int $id, array $data): bool;
    public function getTournamentParticipants(int $torneoId): Collection;
}
```

### **IPartidaRepository**
```php
interface IPartidaRepository {
    public function findByTournament(int $torneoId): Collection;
    public function findByRound(int $rondaId): Collection;
    public function create(array $data): Partida;
    public function updateResult(int $partidaId, float $resultado): bool;
    public function getPlayerGames(string $miembroId, int $torneoId): Collection;
}
```

### **IFideRepository**
```php
interface IFideRepository {
    public function findByMember(string $cedula): ?Fide;
    public function getEloHistory(string $fideId): Collection;
    public function updateElo(string $fideId, array $eloData): bool;
    public function getFederationMembers(string $federationId): Collection;
}
```

## Servicios de Dominio

### **PairingService**
- SwissPairingService
- TeamPairingService
- PairingOptimizerService
- PairingSimulationService

### **AuditService**
- Logging de acciones
- Trazabilidad de cambios
- Historial de auditoría

### **PermissionService**
- Verificación de permisos
- Gestión de roles
- Control de acceso

### **MailService**
- BrevoMailService
- Notificaciones
- Recuperación de contraseñas

## Recomendaciones de Refactoring

Para implementar un patrón Repository completo, se recomienda:

1. **Crear interfaces de repositorio** para cada entidad principal
2. **Implementar repositorios concretos** que encapsulen la lógica de acceso a datos
3. **Migrar consultas PDO directas** a métodos de repositorio
4. **Inyectar repositorios** en controladores y servicios
5. **Implementar Unit of Work** para transacciones complejas

## Conclusión

El proyecto actual utiliza una arquitectura funcional pero mixta. La implementación de interfaces de repositorio mejoraría la:
- **Testabilidad** del código
- **Mantenibilidad** de las consultas
- **Separación de responsabilidades**
- **Flexibilidad** para cambios de base de datos
