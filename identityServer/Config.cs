// Copyright (c) Brock Allen & Dominick Baier. All rights reserved.
// Licensed under the Apache License, Version 2.0. See LICENSE in the project root for license information.

using IdentityModel;
using IdentityServer4;
using IdentityServer4.Models;
using System.Collections.Generic;
using System.Security.Claims;

namespace IdentityServer
{
    public class Config
    {
        // scopes define the resources in your system
        public static IEnumerable<IdentityResource> GetIdentityResources()
        {
            return new List<IdentityResource>
            {
                new IdentityResources.OpenId(),
                new IdentityResources.Profile(),
                new IdentityResources.Email(),
                new IdentityResources.Address(),
            };
        }

        public static IEnumerable<ApiResource> GetApiResources()
        {
            return new List<ApiResource>
            {
                //new ApiResource("api1", "My API")
                new ApiResource
                {
                    Name = "FileServerApi",

                    // secret for using introspection endpoint
                    ApiSecrets =
                    {
                        new Secret("FileServerSecret".Sha256())
                    },

                    // include the following using claims in access token (in addition to subject id)
                    UserClaims = { JwtClaimTypes.Name, JwtClaimTypes.Email },

                    // Scopes for the API
                    Scopes =
                    {
                        new Scope()
                        {
                            Name = "FileServer.full_access",
                            DisplayName = "Full access to FileServer",
                         }
                    }
                }
            };
        }

        // clients want to access resources (aka scopes)
        public static IEnumerable<Client> GetClients()
        {
            // client credentials client
            return new List<Client>
            {
                new Client
                {
                    ClientId = "HawaiDashboard",
                    ClientName = "HawaiDashboard",

                    AccessTokenType = AccessTokenType.Reference,
                    AllowedGrantTypes = GrantTypes.Implicit,
                    AllowAccessTokensViaBrowser = true,
                    RequireConsent = false,

                    RedirectUris =           { "http://localhost:4200/home" },
                    PostLogoutRedirectUris = { "http://localhost:4200" },
                    AllowedCorsOrigins =     { "http://localhost:4200" },

                    AllowedScopes =
                    {
                        IdentityServerConstants.StandardScopes.OpenId,
                        IdentityServerConstants.StandardScopes.Profile,
                        IdentityServerConstants.StandardScopes.Email,
                        IdentityServerConstants.StandardScopes.Address,
                        "FileServer.full_access"
                    }
                },

                new Client
                {
                    ClientId = "testClient",
                    ClientName = "testClient",

                    AccessTokenType = AccessTokenType.Reference,
                    //AccessTokenType = AccessTokenType.Jwt,
                    AllowedGrantTypes = GrantTypes.Implicit,
                    AllowAccessTokensViaBrowser = true,
                    RequireConsent = false,

                    // könnte von Vorteil sein bei, instrospectionpoint später
                    // AlwaysIncludeUserClaimsInIdToken = true,

                    RedirectUris =           { "http://localhost:4201/home" },
                    PostLogoutRedirectUris = { "http://localhost:4201" },
                    AllowedCorsOrigins =     { "http://localhost:4201" },

                    AllowedScopes =
                    {
                        IdentityServerConstants.StandardScopes.OpenId,
                        IdentityServerConstants.StandardScopes.Profile,
                        IdentityServerConstants.StandardScopes.Email,
                        "FileServer.full_access"
                     }
                }
            };
        }
    }
}